<?namespace Intervolga\Migrato\Data\Module\Sale;

use Bitrix\Main\Loader;
use Bitrix\Sale\Internals\PersonTypeSiteTable;
use Bitrix\Sale\Internals\PersonTypeTable;
use Intervolga\Migrato\Data\BaseData;
use Intervolga\Migrato\Data\Record;
use Intervolga\Migrato\Data\RecordId;
use Intervolga\Migrato\Tool\XmlIdProvider\UfXmlIdProvider;

class PersonType extends BaseData
{
	public function __construct()
	{
		Loader::includeModule("sale");
		$this->xmlIdProvider = new UfXmlIdProvider($this);
	}

	public function getList(array $filter = array())
	{
		$result = array();
		$personTypesSites = $this->getPersonTypesSites();
		$getList = PersonTypeTable::getList();
		while ($personType = $getList->fetch())
		{
			$record = new Record($this);
			$id = RecordId::createNumericId($personType["ID"]);
			$record->setId($id);
			$record->setXmlId(
				$this->getXmlIdProvider()->getXmlId($id)
			);
			$record->setFields(array(
				"NAME" => $personType["NAME"],
				"SORT" => $personType["SORT"],
				"ACTIVE" => $personType["ACTIVE"],
				"LID" => $personTypesSites[$personType["ID"]],
			));
			$result[] = $record;
		}

		return $result;
	}

	/**
	 * @return array
	 */
	protected function getPersonTypesSites()
	{
		$result = array();
		$getList = PersonTypeSiteTable::getList();
		while ($personTypeSite = $getList->fetch())
		{
			$result[$personTypeSite["PERSON_TYPE_ID"]][] = $personTypeSite["SITE_ID"];
		}
		return $result;
	}
}