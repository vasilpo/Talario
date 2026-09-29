import {
  createDecipheriv,
  createHash,
  createPrivateKey,
  createPublicKey,
  diffieHellman,
  hkdfSync,
} from 'node:crypto';
import fs from 'node:fs';

const [keyPath, envelopePath, outPath] = process.argv.slice(2);
if (!keyPath || !envelopePath || !outPath) throw new Error('ARGS_REQUIRED');

const pem = fs.readFileSync(keyPath, 'utf8');
const body = pem.split(/\r?\n/).filter((line) => line && !line.startsWith('-----')).join('');
const rawKey = Buffer.from(body, 'base64');
const magic = Buffer.from('openssh-key-v1\0');
if (!rawKey.subarray(0, magic.length).equals(magic)) throw new Error('OPENSSH_KEY_FORMAT_INVALID');

let pos = magic.length;
function readString(buf) {
  if (pos + 4 > buf.length) throw new Error('OPENSSH_KEY_PARSE_FAILED');
  const n = buf.readUInt32BE(pos); pos += 4;
  const end = pos + n;
  if (end > buf.length) throw new Error('OPENSSH_KEY_PARSE_FAILED');
  const value = buf.subarray(pos, end); pos = end;
  return value;
}
const cipher = readString(rawKey);
const kdf = readString(rawKey);
readString(rawKey);
if (cipher.toString() !== 'none' || kdf.toString() !== 'none') throw new Error('ENCRYPTED_SIGNING_KEY_UNSUPPORTED');
if (pos + 4 > rawKey.length) throw new Error('OPENSSH_KEY_PARSE_FAILED');
const nkeys = rawKey.readUInt32BE(pos); pos += 4;
if (nkeys !== 1) throw new Error('OPENSSH_KEY_COUNT_INVALID');
readString(rawKey);
const privateBlock = readString(rawKey);

let q = 0;
if (privateBlock.length < 8) throw new Error('OPENSSH_PRIVATE_BLOCK_INVALID');
const check1 = privateBlock.readUInt32BE(q); q += 4;
const check2 = privateBlock.readUInt32BE(q); q += 4;
if (check1 !== check2) throw new Error('OPENSSH_CHECKINT_INVALID');
function privateString() {
  if (q + 4 > privateBlock.length) throw new Error('OPENSSH_PRIVATE_PARSE_FAILED');
  const n = privateBlock.readUInt32BE(q); q += 4;
  const end = q + n;
  if (end > privateBlock.length) throw new Error('OPENSSH_PRIVATE_PARSE_FAILED');
  const value = privateBlock.subarray(q, end); q = end;
  return value;
}
const keyType = privateString();
const publicEd = privateString();
const privateEd = privateString();
privateString();
if (keyType.toString() !== 'ssh-ed25519' || publicEd.length !== 32 || privateEd.length !== 64) {
  throw new Error('OPENSSH_ED25519_PRIVATE_INVALID');
}
if (!privateEd.subarray(32).equals(publicEd)) throw new Error('OPENSSH_ED25519_PUBLIC_MISMATCH');

const seed = privateEd.subarray(0, 32);
const h = createHash('sha512').update(seed).digest();
const scalar = Buffer.from(h.subarray(0, 32));
scalar[0] &= 248;
scalar[31] &= 127;
scalar[31] |= 64;
const xPrivateDer = Buffer.concat([Buffer.from('302e020100300506032b656e04220420', 'hex'), scalar]);
const xPrivate = createPrivateKey({ key: xPrivateDer, format: 'der', type: 'pkcs8' });

const env = JSON.parse(fs.readFileSync(envelopePath, 'utf8'));
const allowed = {
  'talario.part-sync.encrypted-create.v1': {
    aad: 'talario-part-sync-step6-create-v1',
    plaintextSha256: '0e4c0eeccb2ffac735d026f0962d5fdb4ca138aaafa21bf8a9273d60c60cac3c',
    marker: 'STEP6_PAYLOAD_DECRYPT=PASS',
  },
  'talario.part-sync.encrypted-handoff-url.v1': {
    aad: 'talario-part-sync-step6-handoff-url-v1',
    plaintextSha256: 'ebec14d7eba5fe2e45a317ddb2d0408b345a6c5986ef3fee04e998c11297bb5f',
    plaintextBytes: 740,
    legacyEnvelopeSha256: '8cb0d08f8ced653f16847ab9cc669e24453e8130f885819516cf73148c887f0a',
    legacyEnvelopeBytes: 776,
    marker: 'STEP6_HANDOFF_DECRYPT=PASS',
  },
};
const policy = allowed[env.schema_version];
if (!policy) throw new Error('ENVELOPE_SCHEMA_INVALID');
if (env.aad !== policy.aad) throw new Error('ENVELOPE_AAD_INVALID');
if (policy.legacyEnvelopeSha256) {
  if (env.plaintext_sha256 !== policy.legacyEnvelopeSha256) throw new Error('LEGACY_ENVELOPE_SHA256_MISMATCH');
  if (Number(env.plaintext_bytes) !== policy.legacyEnvelopeBytes) throw new Error('LEGACY_ENVELOPE_BYTES_MISMATCH');
} else if (env.plaintext_sha256 !== policy.plaintextSha256) {
  throw new Error('PINNED_PLAINTEXT_SHA256_MISMATCH');
}

const eph = Buffer.from(env.ephemeral_x25519_public_b64, 'base64');
if (eph.length !== 32) throw new Error('EPHEMERAL_PUBLIC_INVALID');
const xPublicDer = Buffer.concat([Buffer.from('302a300506032b656e032100', 'hex'), eph]);
const xPublic = createPublicKey({ key: xPublicDer, format: 'der', type: 'spki' });
const shared = diffieHellman({ privateKey: xPrivate, publicKey: xPublic });
const aad = Buffer.from(env.aad, 'utf8');
const key = Buffer.from(hkdfSync('sha256', shared, Buffer.alloc(0), aad, 32));

const nonce = Buffer.from(env.nonce_b64, 'base64');
const combined = Buffer.from(env.ciphertext_b64, 'base64');
if (nonce.length !== 12 || combined.length < 17) throw new Error('CIPHERTEXT_INVALID');
const ciphertext = combined.subarray(0, combined.length - 16);
const tag = combined.subarray(combined.length - 16);
const decipher = createDecipheriv('chacha20-poly1305', key, nonce, { authTagLength: 16 });
decipher.setAAD(aad);
decipher.setAuthTag(tag);
const plaintext = Buffer.concat([decipher.update(ciphertext), decipher.final()]);

const expectedBytes = policy.plaintextBytes ?? Number(env.plaintext_bytes);
if (plaintext.length !== expectedBytes) throw new Error('PLAINTEXT_BYTES_MISMATCH');
const sha = createHash('sha256').update(plaintext).digest('hex');
if (sha !== policy.plaintextSha256) throw new Error('PLAINTEXT_SHA256_MISMATCH');
fs.writeFileSync(outPath, plaintext, { mode: 0o600 });
console.log(policy.marker);
