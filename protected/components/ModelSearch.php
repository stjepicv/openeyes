<?php
/**
 * Created by PhpStorm.
 * User: petergallagher
 * Date: 30/03/15
 * Time: 13:25
 */

/**
 * ModelSearch class allows for generic searching of a model.
 *
 * With the ModelSearch class it is possible to specify through paramaters which attributes for a model should be
 * searchable and how. It will then use GenericSearch widget to output a form in a view based on the same
 * configuration. To make a model attribute searchable it needs to be added to the searchItems array.
 *
 * It is possible to assign an array of configuration options to this entry in searchItems which will change how the
 * database is queried. For example an array containing compare_to which has an array of other attributes of the model
 * will cause the query to be made against all attributes listed. eg:
 * 	$search->addSearchItem('name', array(
 * 		'compare_to' => array(
 * 			'pas_code',
 * 			'consultant.first_name',
 * 			'consultant.last_name',
 * 		)
 *	));
 *
 * As you can see this works with across relationships, however for clarity in output you need to add the labels to
 * the attributeLabels array of the model ModelSearch was instantiated with.
 *
 */
class ModelSearch
{
	/**
	 * @var BaseActiveRecord
	 */
	protected $model;

	/**
	 * @var CDbCriteria
	 */
	protected $criteria;

	/**
	 * @var Request
	 */
	protected $request;

	/**
	 * @var int
	 */
	protected $itemsPerPage = 30;

	/**
	 * @var array
	 */
	protected $searchItems = array();

	/**
	 * @var array
	 */
	protected $searchTerms = array();

	/**
	 * @return BaseActiveRecord
	 */
	public function getModel()
	{
		return $this->model;
	}

	/**
	 * @param BaseActiveRecord $model
	 */
	public function setModel($model)
	{
		$this->model = $model;
	}

	/**
	 * @return CDbCriteria
	 */
	public function getCriteria()
	{
		return $this->criteria;
	}

	/**
	 * @param CDbCriteria $criteria
	 */
	public function setCriteria($criteria)
	{
		$this->criteria = $criteria;
	}

	/**
	 * @return mixed
	 */
	public function getRequest()
	{
		return $this->request;
	}

	/**
	 * @param mixed $request
	 */
	public function setRequest($request)
	{
		$this->request = $request;
	}

	/**
	 * @return int
	 */
	public function getItemsPerPage()
	{
		return $this->itemsPerPage;
	}

	/**
	 * @param int $itemsPerPage
	 */
	public function setItemsPerPage($itemsPerPage)
	{
		$this->itemsPerPage = $itemsPerPage;
	}

	/**
	 * @return array
	 */
	public function getSearchItems()
	{
		return $this->searchItems;
	}

	/**
	 * @param array $searchItems
	 */
	public function setSearchItems($searchItems)
	{
		$this->searchItems = $searchItems;
	}

	/**
	 * @param BaseActiveRecord $model
	 */
	public function __construct(BaseActiveRecord $model)
	{
		$this->model = $model;
		$this->criteria = new CDbCriteria();
		$this->criteria->with = array();
		$this->request = $request = Yii::app()->getRequest();
		$this->generateCriteria();
	}

	/**
	 * @param string $attr
	 */
	protected function generateCriteria($attr = 'search')
	{
		$search = $this->request->getParam($attr);
		$sensitive = $this->request->getParam('case_sensitive', false);

		if(is_array($search)){
			foreach($search as $key => $value){
				if(!is_array($value)){
					$this->addCompare($this->criteria, $key, $value, $sensitive);
				} else {
					if(!isset($value['value'])){
						//no value provided to search against
						continue;
					}
					$searchTerm = $value['value'];
					$this->addCompare($this->criteria, $key, $searchTerm, $sensitive);
					if(array_key_exists('compare_to', $value) && is_array($value['compare_to'])){
						foreach($value['compare_to'] as $compareTo)
						{
							$this->addCompare($this->criteria, $compareTo, $searchTerm, $sensitive, 'OR');
						}
					}
				}
			}
		}
	}

	/**
	 * Adds a comparison betwe
	 * @param CDbCriteria $criteria
	 * @param $attribute
	 * @param $value
	 * @param bool $sensitive
	 * @param string $operator
	 */
	protected function addCompare(CDbCriteria $criteria, $attribute, $value, $sensitive = false, $operator = 'AND')
	{
		$search = $attribute;
		$search = $this->relationalAttribute($criteria, $attribute, $search);

		if($value !== '' ){
			if ($sensitive) {
				$criteria->compare('LOWER(' . $search . ')', strtolower($value), true, $operator);
			} else {
				$criteria->compare($search, $value, true, $operator);
			}
			$this->searchTerms[$attribute] = $value;
		}
	}

	/**
	 * Inits pagination for the results and returns it.
	 *
	 * @return CPagination
	 */
	public function initPagination()
	{
		$itemsCount = $this->model->count($this->criteria);
		$pagination = new CPagination($itemsCount);
		$pagination->pageSize = $this->itemsPerPage;
		$pagination->applyLimit($this->criteria);
		return $pagination;
	}

	/**
	 * Performs the query that has been generated.
	 *
	 * @return CActiveRecord[]
	 */
	public function retrieveResults()
	{
		return $this->model->findAll($this->criteria);
	}

	/**
	 * Add a search item
	 *
	 * @param $key
	 * @param string $search
	 */
	public function addSearchItem($key, $search = '')
	{
		$this->searchItems[$key] = $search;
	}

	/**
	 * Retrieves the search term supplied by the user for a given attribute if there was one.
	 *
	 * @param $attribute
	 * @param string $default
	 * @return string
	 */
	public function getSearchTermForAttribute($attribute, $default = '')
	{
		if(array_key_exists($attribute, $this->searchTerms)){
			return $this->searchTerms[$attribute];
		}

		return $default;
	}

	/**
	 * Takes an attribute name and makes sure appropriate relationships are included
	 *
	 * This will take an attribute name many layers of relationship deep, make sure that all appropriate tables are
	 * included with the result and return a string that is then acceptable to be used in a where clause.
	 *
	 * @param CDbCriteria $criteria
	 * @param string $attribute
	 * @param string $search
	 * @return string
	 */
	protected function relationalAttribute(CDbCriteria $criteria, $attribute, $search)
	{
		$search = $this->model->getTableAlias() . '.' . $search;

		if (strpos($attribute, '.')) {
			$relationship = explode('.', $attribute);
			$relationshipArray = array();
			while (count($relationship) > 1) {
				$relationshipString = array_shift($relationship);
				$search = $relationshipString;
				if (count($relationshipArray)) {
					$relationshipString = implode('.', $relationshipArray) . '.' . $relationshipString;
				}
				$relationshipArray[] = $relationshipString;
			}
			$search .= '.' . array_shift($relationship);

			$criteria->together = true;
			$criteria->with = array_merge(
				$criteria->with,
				$relationshipArray
			);
		}

		return $search;
	}
}