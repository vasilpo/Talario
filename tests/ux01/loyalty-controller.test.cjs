'use strict';
const test = require('node:test');
const assert = require('node:assert/strict');
const path = require('node:path');
const net = require('node:net');
const {spawn, spawnSync} = require('node:child_process');

const phpBinary = process.env.PHP_BINARY || 'php';
const phpProbe = spawnSync(phpBinary, ['-v'], {encoding: 'utf8'});
const noPhp = phpProbe.error || phpProbe.status !== 0;

async function unusedPort() {
  const socket = net.createServer();
  await new Promise((resolve, reject) => {
    socket.once('error', reject);
    socket.listen(0, '127.0.0.1', resolve);
  });
  const port = socket.address().port;
  await new Promise((resolve, reject) => socket.close(error => error ? reject(error) : resolve()));
  return port;
}

test('loyalty HTTP controller preserves native signed balances and current-account privacy', {
  skip: noPhp ? 'PHP CLI is unavailable; actual PHP controller behavior was NOT verified' : false,
}, async t => {
  const port = await unusedPort();
  const router = path.join(__dirname, 'fixtures/loyalty-controller-router.php');
  const child = spawn(phpBinary, ['-n', '-d', 'display_errors=stderr', '-d', 'log_errors=0', '-S', '127.0.0.1:' + port, router], {
    stdio: ['ignore', 'ignore', 'pipe'],
  });
  let serverLog = '';
  child.stderr.on('data', chunk => { serverLog += chunk.toString(); });
  t.after(async () => {
    if (child.exitCode !== null || child.signalCode !== null) { return; }
    await new Promise(resolve => {
      child.once('exit', resolve);
      child.kill('SIGTERM');
    });
  });
  await new Promise((resolve, reject) => {
    const timeout = setTimeout(() => reject(new Error('PHP test server did not start: ' + serverLog)), 5000);
    const done = error => {
      clearTimeout(timeout);
      child.stderr.removeListener('data', inspect);
      child.removeListener('error', failed);
      child.removeListener('exit', exited);
      error ? reject(error) : resolve();
    };
    const inspect = () => { if (/Development Server .* started/.test(serverLog)) { done(); } };
    const failed = error => done(error);
    const exited = code => done(new Error('PHP test server exited with ' + code + ': ' + serverLog));
    child.stderr.on('data', inspect);
    child.once('error', failed);
    child.once('exit', exited);
    inspect();
  });

  const allowedCases = [
    ['positive', true, 145, true],
    ['zero', true, 0, true],
    ['missing-row', true, 0, true],
    ['empty-row', true, 0, true],
    ['null-row', true, 0, true],
    ['numeric-string', true, 57, true],
    ['spoofed-id', true, 145, true],
    ['guest', false, null, false],
    ['guest-spoofed-id', false, null, false],
    ['missing-constant', true, null, false],
    ['missing-reader', true, null, false],
    ['missing-both', true, null, false],
    ['signed-negative', true, -100, true],
    ['signed-negative-string', true, -100, true],
    ['invalid-string', true, null, true],
    ['invalid-array', true, null, true],
    ['rewrite', true, 145, true],
  ];
  for (const [name, authenticated, points, readsCurrentUser] of allowedCases) {
    await t.test(name, async () => {
      const response = await fetch('http://127.0.0.1:' + port + '/?fixture=' + name);
      assert.equal(response.status, 200, name);
      assert.deepEqual(await response.json(), {authenticated, points}, name);
      assert.match(response.headers.get('content-type') || '', /^application\/json; charset=utf-8$/i);
      assert.match(response.headers.get('cache-control') || '', /private/);
      assert.match(response.headers.get('cache-control') || '', /no-store/);
      assert.match(response.headers.get('cache-control') || '', /max-age=0/);
      assert.equal(response.headers.get('pragma'), 'no-cache');
      assert.match(response.headers.get('vary') || '', /(?:^|,\s*)Cookie(?:,|$)/i);
      assert.equal(response.headers.get('x-content-type-options'), 'nosniff');
      assert.equal(response.headers.get('x-fixture-reward-read'), readsCurrentUser ? '["W",42]' : null,
        'a request must never read the supplied user_id or a guest balance');
    });
  }
  for (const name of ['wrong-area', 'wrong-mode', 'prod-uri', 'outside-config']) {
    await t.test('rejects ' + name, async () => {
      const response = await fetch('http://127.0.0.1:' + port + '/?fixture=' + name);
      assert.equal(response.status, 404);
      assert.deepEqual(await response.json(), {controller_status: [404]});
      assert.equal(response.headers.get('x-fixture-reward-read'), null);
    });
  }
  assert.doesNotMatch(serverLog, /(?:PHP )?(?:Fatal error|Warning|Parse error|Deprecated):/i);
});
