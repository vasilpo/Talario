<?php

namespace Tygh {
    class Registry
    {
        private static $values = [];

        public static function get($key)
        {
            return self::$values[$key] ?? null;
        }

        public static function set($key, $value)
        {
            self::$values[$key] = $value;
        }
    }
}

namespace {
    use RuntimeException;
    use Tygh\Addons\TalarioScheduleResources\Repository\LocationRepository;
    use Tygh\Addons\TalarioScheduleResources\Repository\OccurrenceRepository;
    use Tygh\Addons\TalarioScheduleResources\Repository\ResourceProductRepository;
    use Tygh\Addons\TalarioScheduleResources\Repository\ResourceRepository;
    use Tygh\Addons\TalarioScheduleResources\Repository\ScheduleRuleRepository;
    use Tygh\Addons\TalarioScheduleResources\Service\ScheduleResourceService;
    use Tygh\Registry;

    define('AREA', 'A');
    define('TIME', 1_757_635_200);

    $root = dirname(__DIR__, 2);
    $base = $root . '/app/addons/talario_schedule_resources/Tygh/Addons/TalarioScheduleResources';
    require_once $base . '/Repository/LocationRepository.php';
    require_once $base . '/Repository/ResourceRepository.php';
    require_once $base . '/Repository/ScheduleRuleRepository.php';
    require_once $base . '/Repository/OccurrenceRepository.php';
    require_once $base . '/Repository/ResourceProductRepository.php';
    require_once $base . '/Service/ScheduleResourceService.php';

    final class FakeLocationRepository extends LocationRepository
    {
        public function find($id) { return null; }
    }

    final class FakeResourceRepository extends ResourceRepository
    {
        private $items;

        public function __construct(array $items) { $this->items = $items; }
        public function find($id) { return $this->items[$id] ?? null; }
    }

    final class FakeScheduleRuleRepository extends ScheduleRuleRepository {}
    final class FakeOccurrenceRepository extends OccurrenceRepository {}

    final class FakeResourceProductRepository extends ResourceProductRepository
    {
        private $products;
        public $added = [];

        public function __construct(array $products) { $this->products = $products; }
        public function findProduct($id) { return $this->products[$id] ?? null; }
        public function exists($product_id, $resource_id) { return false; }
        public function add($product_id, $resource_id) { $this->added[] = [(int) $product_id, (int) $resource_id]; }
        public function findResourcesByProduct($product_id, $company_id) { return [1]; }
    }

    function assert_same($expected, $actual, $message)
    {
        if ($expected !== $actual) {
            throw new RuntimeException($message . ': expected ' . var_export($expected, true) . ', got ' . var_export($actual, true));
        }
    }

    function expect_runtime_exception(callable $operation, $expected_message)
    {
        try {
            $operation();
        } catch (RuntimeException $exception) {
            assert_same($expected_message, $exception->getMessage(), 'Unexpected exception message');
            return;
        }
        throw new RuntimeException('Expected RuntimeException was not thrown: ' . $expected_message);
    }

    $products = new FakeResourceProductRepository([
        101 => ['product_id' => 101, 'company_id' => 10],
        201 => ['product_id' => 201, 'company_id' => 20],
    ]);
    $service = new ScheduleResourceService(
        new FakeLocationRepository(),
        new FakeResourceRepository([
            1 => ['resource_id' => 1, 'company_id' => 10],
            2 => ['resource_id' => 2, 'company_id' => 20],
        ]),
        new FakeScheduleRuleRepository(),
        new FakeOccurrenceRepository(),
        $products
    );

    Registry::set('runtime.company_id', 10);
    assert_same(10, (int) $service->getResource(1, 20)['company_id'], 'Runtime company must override admin_company_id');
    expect_runtime_exception(static function () use ($service) {
        $service->getResource(2, 20);
    }, 'Cross-company operation is forbidden');

    $service->addProductResource(101, 1);
    assert_same([[101, 1]], $products->added, 'Same-company product/resource mapping must be created');
    expect_runtime_exception(static function () use ($service) {
        $service->addProductResource(201, 1);
    }, 'Cross-company operation is forbidden');
    expect_runtime_exception(static function () use ($service) {
        $service->getResourcesForProduct(201);
    }, 'Cross-company operation is forbidden');

    Registry::set('runtime.company_id', 0);
    assert_same(20, (int) $service->getResource(2, 20)['company_id'], 'Admin company fallback must work only in admin area');
    expect_runtime_exception(static function () use ($service) {
        $service->getResource(1);
    }, 'Company context is required');

    echo "PASS: company boundary\n";
}
