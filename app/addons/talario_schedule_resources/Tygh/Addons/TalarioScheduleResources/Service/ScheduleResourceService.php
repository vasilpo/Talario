<?php

namespace Tygh\Addons\TalarioScheduleResources\Service;

use DateTimeImmutable;
use InvalidArgumentException;
use RuntimeException;
use Tygh\Addons\TalarioScheduleResources\Repository\LocationRepository;
use Tygh\Addons\TalarioScheduleResources\Repository\OccurrenceRepository;
use Tygh\Addons\TalarioScheduleResources\Repository\ResourceProductRepository;
use Tygh\Addons\TalarioScheduleResources\Repository\ResourceRepository;
use Tygh\Addons\TalarioScheduleResources\Repository\ScheduleRuleRepository;
use Tygh\Registry;

/**
 * Application boundary for the physical schedule model.
 *
 * Controllers must not bypass this class: every public operation resolves the
 * acting company from runtime context and checks ownership before persistence.
 */
class ScheduleResourceService
{
    private $locations;
    private $resources;
    private $rules;
    private $occurrences;
    private $products;

    public function __construct(
        LocationRepository $locations = null,
        ResourceRepository $resources = null,
        ScheduleRuleRepository $rules = null,
        OccurrenceRepository $occurrences = null,
        ResourceProductRepository $products = null
    ) {
        $this->locations = $locations ?: new LocationRepository();
        $this->resources = $resources ?: new ResourceRepository();
        $this->rules = $rules ?: new ScheduleRuleRepository();
        $this->occurrences = $occurrences ?: new OccurrenceRepository();
        $this->products = $products ?: new ResourceProductRepository();
    }

    public function createLocation(array $data, $admin_company_id = null)
    {
        $company_id = $this->actingCompanyId($admin_company_id);
        $now = TIME;
        return $this->locations->create($this->only($data, ['name', 'address', 'address_details', 'latitude', 'longitude', 'status']) + [
            'company_id' => $company_id, 'created_at' => $now, 'updated_at' => $now,
        ]);
    }

    public function getLocation($id, $admin_company_id = null)
    {
        $location = $this->requireLocation($id);
        $this->assertCompany($location['company_id'], $this->actingCompanyId($admin_company_id));
        return $location;
    }

    public function updateLocation($id, array $data, $admin_company_id = null)
    {
        $this->getLocation($id, $admin_company_id);
        $data = $this->only($data, ['name', 'address', 'address_details', 'latitude', 'longitude', 'status']);
        $data['updated_at'] = TIME;
        $this->locations->update($id, $data);
    }

    public function archiveLocation($id, $admin_company_id = null) { $this->updateLocation($id, ['status' => 'D'], $admin_company_id); }
    public function getLocations($admin_company_id = null) { return $this->locations->findByCompany($this->actingCompanyId($admin_company_id)); }
    public function locationBelongsToCompany($id, $company_id) { $location = $this->locations->find($id); return $location && (int) $location['company_id'] === (int) $company_id; }

    public function createResource(array $data, $admin_company_id = null)
    {
        $company_id = $this->actingCompanyId($admin_company_id);
        $now = TIME;
        return $this->resources->create($this->only($data, ['name', 'status']) + ['company_id' => $company_id, 'created_at' => $now, 'updated_at' => $now]);
    }

    public function getResource($id, $admin_company_id = null)
    {
        $resource = $this->requireResource($id);
        $this->assertCompany($resource['company_id'], $this->actingCompanyId($admin_company_id));
        return $resource;
    }

    public function updateResource($id, array $data, $admin_company_id = null)
    {
        $this->getResource($id, $admin_company_id);
        $data = $this->only($data, ['name', 'status']);
        $data['updated_at'] = TIME;
        $this->resources->update($id, $data);
    }

    public function archiveResource($id, $admin_company_id = null) { $this->updateResource($id, ['status' => 'D'], $admin_company_id); }
    public function getResources($admin_company_id = null) { return $this->resources->findByCompany($this->actingCompanyId($admin_company_id)); }

    public function createRule(array $data, $admin_company_id = null)
    {
        $resource = $this->getResource($data['resource_id'], $admin_company_id);
        $this->assertLocationCompany($data['location_id'], $resource['company_id']);
        $this->validateRule($data);
        return $this->rules->create($this->only($data, ['resource_id', 'location_id', 'weekday', 'starts_time', 'duration_minutes', 'capacity', 'valid_from', 'valid_to', 'status']));
    }

    public function updateRule($id, array $data, $admin_company_id = null)
    {
        $rule = $this->requireRule($id);
        $resource = $this->getResource($rule['resource_id'], $admin_company_id);
        if (isset($data['resource_id']) && (int) $data['resource_id'] !== (int) $rule['resource_id']) {
            throw new InvalidArgumentException('A schedule rule cannot be moved to another resource');
        }
        $merged = array_merge($rule, $data);
        $this->assertLocationCompany($merged['location_id'], $resource['company_id']);
        $this->validateRule($merged);
        $this->rules->update($id, $this->only($data, ['location_id', 'weekday', 'starts_time', 'duration_minutes', 'capacity', 'valid_from', 'valid_to', 'status']));
    }

    public function disableRule($id, $admin_company_id = null) { $this->updateRule($id, ['status' => 'D'], $admin_company_id); }
    public function getRules($resource_id, $admin_company_id = null) { $this->getResource($resource_id, $admin_company_id); return $this->rules->findByResource($resource_id); }

    public function addProductResource($product_id, $resource_id, $admin_company_id = null)
    {
        $resource = $this->getResource($resource_id, $admin_company_id);
        $product = $this->products->findProduct($product_id);
        if (!$product) { throw new InvalidArgumentException('Product does not exist'); }
        $this->assertCompany($product['company_id'], $resource['company_id']);
        if (!$this->products->exists($product_id, $resource_id)) { $this->products->add($product_id, $resource_id); }
    }

    public function removeProductResource($product_id, $resource_id, $admin_company_id = null)
    {
        $resource = $this->getResource($resource_id, $admin_company_id);
        $product = $this->products->findProduct($product_id);
        if (!$product) { throw new InvalidArgumentException('Product does not exist'); }
        $this->assertCompany($product['company_id'], $resource['company_id']);
        $this->products->remove($product_id, $resource_id);
    }
    public function getResourcesForProduct($product_id, $admin_company_id = null)
    {
        $product = $this->products->findProduct($product_id);
        if (!$product) { throw new InvalidArgumentException('Product does not exist'); }
        $this->assertCompany($product['company_id'], $this->actingCompanyId($admin_company_id));
        return $this->products->findResourcesByProduct($product_id, $product['company_id']);
    }
    public function getProductsForResource($resource_id, $admin_company_id = null)
    {
        $resource = $this->getResource($resource_id, $admin_company_id);
        return $this->products->findProductsByResource($resource_id, $resource['company_id']);
    }

    public function createOccurrence(array $data, $admin_company_id = null)
    {
        $resource = $this->getResource($data['resource_id'], $admin_company_id);
        $rule = empty($data['rule_id']) ? null : $this->requireRule($data['rule_id']);
        if ($rule && (int) $rule['resource_id'] !== (int) $resource['resource_id']) { throw new InvalidArgumentException('Rule does not belong to resource'); }
        if ($rule) {
            $data['location_id'] = $rule['location_id'];
        } elseif (empty($data['location_id'])) {
            throw new InvalidArgumentException('Location is required for an occurrence without a rule');
        }
        $this->assertLocationCompany($data['location_id'], $resource['company_id']);
        if ((int) $data['capacity'] <= 0) { throw new InvalidArgumentException('Capacity must be positive'); }
        if (new DateTimeImmutable($data['ends_at']) <= new DateTimeImmutable($data['starts_at'])) { throw new InvalidArgumentException('Occurrence end must be after start'); }
        $now = TIME;
        return $this->occurrences->create($this->only($data, ['resource_id', 'rule_id', 'location_id', 'starts_at', 'ends_at', 'capacity', 'status']) + ['created_at' => $now, 'updated_at' => $now]);
    }

    public function getOccurrence($id, $admin_company_id = null) { $item = $this->occurrences->find($id); if (!$item) { throw new InvalidArgumentException('Occurrence does not exist'); } $this->getResource($item['resource_id'], $admin_company_id); return $item; }
    public function getOccurrences($resource_id, $from, $to, $admin_company_id = null) { $this->getResource($resource_id, $admin_company_id); if (new DateTimeImmutable($to) <= new DateTimeImmutable($from)) { throw new InvalidArgumentException('Invalid date range'); } return $this->occurrences->findByResourceAndRange($resource_id, $from, $to); }

    private function actingCompanyId($admin_company_id)
    {
        $runtime_company_id = (int) Registry::get('runtime.company_id');
        if ($runtime_company_id > 0) { return $runtime_company_id; }
        if (defined('AREA') && AREA === 'A' && (int) $admin_company_id > 0) { return (int) $admin_company_id; }
        throw new RuntimeException('Company context is required');
    }

    private function assertCompany($actual, $expected) { if ((int) $actual !== (int) $expected) { throw new RuntimeException('Cross-company operation is forbidden'); } }
    private function assertLocationCompany($id, $company_id) { if (!$this->locationBelongsToCompany($id, $company_id)) { throw new RuntimeException('Location belongs to another company or does not exist'); } }
    private function requireLocation($id) { $item = $this->locations->find($id); if (!$item) { throw new InvalidArgumentException('Location does not exist'); } return $item; }
    private function requireResource($id) { $item = $this->resources->find($id); if (!$item) { throw new InvalidArgumentException('Resource does not exist'); } return $item; }
    private function requireRule($id) { $item = $this->rules->find($id); if (!$item) { throw new InvalidArgumentException('Schedule rule does not exist'); } return $item; }
    private function only(array $data, array $allowed) { return array_intersect_key($data, array_flip($allowed)); }
    private function validateRule(array $data)
    {
        if ((int) $data['weekday'] < 1 || (int) $data['weekday'] > 7) { throw new InvalidArgumentException('Weekday must be between 1 and 7'); }
        if ((int) $data['duration_minutes'] <= 0 || (int) $data['capacity'] <= 0) { throw new InvalidArgumentException('Duration and capacity must be positive'); }
        if (!empty($data['valid_to']) && $data['valid_to'] < $data['valid_from']) { throw new InvalidArgumentException('valid_to cannot precede valid_from'); }
    }
}
