<?php
namespace Opencart\Admin\Model\Customer;
/**
 * Class Custom Field
 *
 * @package Opencart\Admin\Model\Customer
 */
class CustomField extends \Opencart\System\Engine\Model {
	/**
	 * Add Custom Field
	 *
	 * @param array<string, mixed> $data
	 *
	 * @return int
	 */
	public function addCustomField(array $data): int {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "custom_field` SET `type` = '" . $this->db->escape((string)$data['type']) . "', `value` = '" . $this->db->escape((string)$data['value']) . "', `validation` = '" . $this->db->escape((string)$data['validation']) . "', `location` = '" . $this->db->escape((string)$data['location']) . "', `status` = '" . (bool)($data['status'] ?? 0) . "', `sort_order` = '" . (int)$data['sort_order'] . "'");

		$custom_field_id = $this->db->getLastId();

		foreach ($data['custom_field_description'] as $language_id => $value) {
			$this->addDescription($custom_field_id, $language_id, $value);
		}

		if (isset($data['custom_field_customer_group'])) {
			foreach ($data['custom_field_customer_group'] as $custom_field_customer_group) {
				if (isset($custom_field_customer_group['customer_group_id'])) {
					$this->addCustomerGroup($custom_field_id, $custom_field_customer_group);
				}
			}
		}

		if (isset($data['custom_field_value'])) {
			foreach ($data['custom_field_value'] as $custom_field_value) {
				$this->addValue($custom_field_id, $custom_field_value);

				$this->db->query("INSERT INTO `" . DB_PREFIX . "custom_field_value` SET `custom_field_id` = '" . (int)$custom_field_id . "', `sort_order` = '" . (int)$custom_field_value['sort_order'] . "'");

				$custom_field_value_id = $this->db->getLastId();

				foreach ($custom_field_value['custom_field_value_description'] as $language_id => $custom_field_value_description) {
					$this->db->query("INSERT INTO `" . DB_PREFIX . "custom_field_value_description` SET `custom_field_value_id` = '" . (int)$custom_field_value_id . "', `language_id` = '" . (int)$language_id . "', `custom_field_id` = '" . (int)$custom_field_id . "', `name` = '" . $this->db->escape($custom_field_value_description['name']) . "'");
				}
			}
		}

		return $custom_field_id;
	}

	/**
	 * Edit Custom Field
	 *
	 * @param int                  $custom_field_id
	 * @param array<string, mixed> $data
	 *
	 * @return void
	 */
	public function editCustomField(int $custom_field_id, array $data): void {
		$this->db->query("UPDATE `" . DB_PREFIX . "custom_field` SET `type` = '" . $this->db->escape((string)$data['type']) . "', `value` = '" . $this->db->escape((string)$data['value']) . "', `validation` = '" . $this->db->escape((string)$data['validation']) . "', `location` = '" . $this->db->escape((string)$data['location']) . "', `status` = '" . (bool)($data['status'] ?? 0) . "', `sort_order` = '" . (int)$data['sort_order'] . "' WHERE `custom_field_id` = '" . (int)$custom_field_id . "'");

		$this->deleteDescription($custom_field_id);

		foreach ($data['custom_field_description'] as $language_id => $value) {
			$this->addDescription($custom_field_id, $language_id, $value);
		}

		$this->deleteCustomerGroup($custom_field_id);

		if (isset($data['custom_field_customer_group'])) {
			foreach ($data['custom_field_customer_group'] as $custom_field_customer_group) {
				if (isset($custom_field_customer_group['customer_group_id'])) {
					$this->addCustomerGroup($custom_field_id, $custom_field_customer_group);
				}
			}
		}

		$this->db->query("DELETE FROM `" . DB_PREFIX . "custom_field_value` WHERE `custom_field_id` = '" . (int)$custom_field_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "custom_field_value_description` WHERE `custom_field_id` = '" . (int)$custom_field_id . "'");

		if (isset($data['custom_field_value'])) {
			foreach ($data['custom_field_value'] as $custom_field_value) {
				$this->addValue($custom_field_id, $custom_field_value);
			}
		}
	}

	/**
	 * Delete Custom Field
	 *
	 * @param int $custom_field_id
	 *
	 * @return void
	 */
	public function deleteCustomField(int $custom_field_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "custom_field` WHERE `custom_field_id` = '" . (int)$custom_field_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "custom_field_description` WHERE `custom_field_id` = '" . (int)$custom_field_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "custom_field_customer_group` WHERE `custom_field_id` = '" . (int)$custom_field_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "custom_field_value` WHERE `custom_field_id` = '" . (int)$custom_field_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "custom_field_value_description` WHERE `custom_field_id` = '" . (int)$custom_field_id . "'");
	}

	/**
	 * Get Custom Field
	 *
	 * @param int $custom_field_id
	 *
	 * @return array<string, mixed>
	 */
	public function getCustomField(int $custom_field_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "custom_field` `cf` LEFT JOIN `" . DB_PREFIX . "custom_field_description` `cfd` ON (`cf`.`custom_field_id` = `cfd`.`custom_field_id`) WHERE `cf`.`custom_field_id` = '" . (int)$custom_field_id . "' AND `cfd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	/**
	 * Get Custom Fields
	 *
	 * @param array<string, mixed> $data
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function getCustomFields(array $data = []): array {
		if (empty($data['filter_customer_group_id'])) {
			$sql = "SELECT * FROM `" . DB_PREFIX . "custom_field` `cf` LEFT JOIN `" . DB_PREFIX . "custom_field_description` `cfd` ON (`cf`.`custom_field_id` = `cfd`.`custom_field_id`) WHERE `cfd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "'";
		} else {
			$sql = "SELECT * FROM `" . DB_PREFIX . "custom_field_customer_group` `cfcg` LEFT JOIN `" . DB_PREFIX . "custom_field` `cf` ON (`cfcg`.`custom_field_id` = `cf`.`custom_field_id`) LEFT JOIN `" . DB_PREFIX . "custom_field_description` `cfd` ON (`cf`.`custom_field_id` = `cfd`.`custom_field_id`) WHERE `cfd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "'";
		}

		if (!empty($data['filter_name'])) {
			$sql .= " AND LCASE(`cfd`.`name`) LIKE '" . $this->db->escape(oc_strtolower($data['filter_name']) . '%') . "'";
		}

		if (isset($data['filter_status'])) {
			$sql .= " AND `cf`.`status` = '" . (bool)$data['filter_status'] . "'";
		}

		if (isset($data['filter_location'])) {
			$sql .= " AND `cf`.`location` = '" . $this->db->escape((string)$data['filter_location']) . "'";
		}

		if (!empty($data['filter_customer_group_id'])) {
			$sql .= " AND `cfcg`.`customer_group_id` = '" . (int)$data['filter_customer_group_id'] . "'";
		}

		$sort_data = [
			'cfd.name',
			'cf.type',
			'cf.location',
			'cf.status',
			'cf.sort_order'
		];

		if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
			$sql .= " ORDER BY " . $data['sort'];
		} else {
			$sql .= " ORDER BY `cfd`.`name`";
		}

		if (isset($data['order']) && ($data['order'] == 'DESC')) {
			$sql .= " DESC";
		} else {
			$sql .= " ASC";
		}

		if (isset($data['start']) || isset($data['limit'])) {
			if ($data['start'] < 0) {
				$data['start'] = 0;
			}

			if ($data['limit'] < 1) {
				$data['limit'] = 20;
			}

			$sql .= " LIMIT " . (int)$data['start'] . "," . (int)$data['limit'];
		}

		$query = $this->db->query($sql);

		return $query->rows;
	}

	/**
	 * Get Total Custom Fields
	 *
	 * @return int
	 */
	public function getTotalCustomFields(): int {
		$query = $this->db->query("SELECT COUNT(*) AS `total` FROM `" . DB_PREFIX . "custom_field`");

		return (int)$query->row['total'];
	}

	/**
	 *	Add Description
	 *
	 *
	 * @param int $custom_field_id primary key of the attribute record to be fetched
	 *
	 * @return void
	 */
	public function addDescription(int $custom_field_id, int $language_id, $data): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "custom_field_description` SET `custom_field_id` = '" . (int)$custom_field_id . "', `language_id` = '" . (int)$language_id . "', `name` = '" . $this->db->escape($data['name']) . "'");
	}

	/**
	 *	Delete Description
	 *
	 *
	 * @param int $custom_field_id primary key of the attribute record to be fetched
	 *
	 * @return array<int, array<string, string>> Descriptions sorted by language_id
	 */
	public function deleteDescription(int $custom_field_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "custom_field_description` WHERE `custom_field_id` = '" . (int)$custom_field_id . "'");

	}

	/**
	 * Get Descriptions
	 *
	 * @param int $custom_field_id
	 *
	 * @return array<int, array<string, string>>
	 */
	public function getDescriptions(int $custom_field_id): array {
		$custom_field_data = [];

		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "custom_field_description` WHERE `custom_field_id` = '" . (int)$custom_field_id . "'");

		foreach ($query->rows as $result) {
			$custom_field_data[$result['language_id']] = ['name' => $result['name']];
		}

		return $custom_field_data;
	}

	public function addCustomerGroup(int $custom_field_id, array $data): void {
		$this->db->query("INSERT INTO `" . DB_PREFIX . "custom_field_customer_group` SET `custom_field_id` = '" . (int)$custom_field_id . "', `customer_group_id` = '" . (int)$data['customer_group_id'] . "', `required` = '" . (int)(isset($data['required']) ? 1 : 0) . "'");
	}

	public function deleteCustomerGroup(int $custom_field_id): void {
		$this->db->query("delete FROM `" . DB_PREFIX . "custom_field_customer_group` WHERE `custom_field_id` = '" . (int)$custom_field_id . "'");
	}

	/**
	 * Get Customer Groups
	 *
	 * @param int $custom_field_id
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function getCustomerGroups(int $custom_field_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "custom_field_customer_group` WHERE `custom_field_id` = '" . (int)$custom_field_id . "'");

		return $query->rows;
	}

	public function addValue(int $option_id, array $data): int {

		if ($custom_field_value['custom_field_value_id']) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "custom_field_value` SET `custom_field_value_id` = '" . (int)$custom_field_value['custom_field_value_id'] . "', `custom_field_id` = '" . (int)$custom_field_id . "', `sort_order` = '" . (int)$custom_field_value['sort_order'] . "'");
		} else {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "custom_field_value` SET `custom_field_id` = '" . (int)$custom_field_id . "', `sort_order` = '" . (int)$custom_field_value['sort_order'] . "'");
		}

		$custom_field_value_id = $this->db->getLastId();

		foreach ($custom_field_value['custom_field_value_description'] as $language_id => $custom_field_value_description) {
			$this->db->query("INSERT INTO `" . DB_PREFIX . "custom_field_value_description` SET `custom_field_value_id` = '" . (int)$custom_field_value_id . "', `language_id` = '" . (int)$language_id . "', `custom_field_id` = '" . (int)$custom_field_id . "', `name` = '" . $this->db->escape($custom_field_value_description['name']) . "'");
		}



		return $option_value_id;
	}

	public function deleteValue(int $option_id): void {
		$this->db->query("DELETE FROM `" . DB_PREFIX . "custom_field_value` WHERE `custom_field_id` = '" . (int)$custom_field_id . "'");
		$this->db->query("DELETE FROM `" . DB_PREFIX . "custom_field_value_description` WHERE `custom_field_id` = '" . (int)$custom_field_id . "'");


		$this->deleteValueDescription($option_id);
	}







	/**
	 * Get Value
	 *
	 * @param int $custom_field_value_id
	 *
	 * @return array<string, mixed>
	 */
	public function getValue(int $custom_field_value_id): array {
		$query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "custom_field_value` `cfv` LEFT JOIN `" . DB_PREFIX . "custom_field_value_description` `cfvd` ON (`cfv`.`custom_field_value_id` = `cfvd`.`custom_field_value_id`) WHERE `cfv`.`custom_field_value_id` = '" . (int)$custom_field_value_id . "' AND `cfvd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "'");

		return $query->row;
	}

	/**
	 * Get Values
	 *
	 * @param int $custom_field_id
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function getValues(int $custom_field_id): array {
		$custom_field_value_data = [];

		$custom_field_value_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "custom_field_value` `cfv` LEFT JOIN `" . DB_PREFIX . "custom_field_value_description` `cfvd` ON (`cfv`.`custom_field_value_id` = `cfvd`.`custom_field_value_id`) WHERE `cfv`.`custom_field_id` = '" . (int)$custom_field_id . "' AND `cfvd`.`language_id` = '" . (int)$this->config->get('config_language_id') . "' ORDER BY `cfv`.`sort_order` ASC");

		foreach ($custom_field_value_query->rows as $custom_field_value) {
			$custom_field_value_data[$custom_field_value['custom_field_value_id']] = [
				'custom_field_value_id' => $custom_field_value['custom_field_value_id'],
				'name'                  => $custom_field_value['name']
			];
		}

		return $custom_field_value_data;
	}

	/**
	 * Get Value Descriptions
	 *
	 * @param int $custom_field_id
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function getValueDescriptions(int $custom_field_id): array {
		$custom_field_value_data = [];

		$custom_field_value_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "custom_field_value` WHERE `custom_field_id` = '" . (int)$custom_field_id . "'");

		foreach ($custom_field_value_query->rows as $custom_field_value) {
			$custom_field_value_description_data = [];

			$custom_field_value_description_query = $this->db->query("SELECT * FROM `" . DB_PREFIX . "custom_field_value_description` WHERE `custom_field_value_id` = '" . (int)$custom_field_value['custom_field_value_id'] . "'");

			foreach ($custom_field_value_description_query->rows as $custom_field_value_description) {
				$custom_field_value_description_data[$custom_field_value_description['language_id']] = ['name' => $custom_field_value_description['name']];
			}

			$custom_field_value_data[] = [
				'custom_field_value_id'          => $custom_field_value['custom_field_value_id'],
				'custom_field_value_description' => $custom_field_value_description_data,
				'sort_order'                     => $custom_field_value['sort_order']
			];
		}

		return $custom_field_value_data;
	}
}
