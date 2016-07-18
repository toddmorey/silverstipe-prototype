<?php

// temporarily using Symphony YAML processing for proof of concept
use Yalesov\Yaml\Yaml;

class Page extends SiteTree {

	private static $db = array(
	);

	private static $has_one = array(
	);

}
class Page_Controller extends ContentController {

	public $templates;

	/**
	 * An array of actions that can be accessed via a request. Each array element should be an action name, and the
	 * permissions or conditions required to allow the user to access it.
	 *
	 * <code> 
	 * array (
	 *     'action', // anyone can access this action
	 *     'action' => true, // same as above
	 *     'action' => 'ADMIN', // you must have ADMIN permissions to access this action
	 *     'action' => '->checkAction' // you can only access this action if $this->checkAction() returns true
	 * );
	 * </code>
	 *
	 * @var array
	 */
	private static $allowed_actions = array (
	);

	public function init() {
		parent::init();
		// You can include any CSS or JS required by your project here.
		// See: http://doc.silverstripe.org/framework/en/reference/requirements

		//build a template name based off of the path
		$templateName = $this->templateName();

        $action = $this->owner->request->param('Action');
        if(!$action) $action = 'index';

		// attach custom JS, if any
		Requirements::javascript($this->ThemeDir() . "/javascript/" . $templateName . ".js");

		// attach custom CSS, if any
		Requirements::CSS($this->ThemeDir() . "/css/" . $templateName . ".css");		

		// render with either unique page template or other defaults
        $this->owner->templates[$action] = array($templateName, 'Page');
	}

	// pull a template name based off of URL of page
	private function templateName() {
		$templateName = trim($this->Link(), "/");
		return str_replace("/", "-", $templateName);
	}	

	// pull yaml from the data file if it exists
	// A simple hack at replicating frontmatter
	public function getYaml() {
		$datamode = $this->getRequest()->getVar('datamode');
		if($datamode) {
			$parsedArray = Yaml::parse('../yaml/' . $this->templateName() . '_' . $datamode . '.yml');
		} else {
			$parsedArray = Yaml::parse('../yaml/' . $this->templateName() . '.yml');	
		}
		return $this->array_to_viewabledata($parsedArray);
	}

	// a hacky stab at turning a nested array into DataObjectLists and DataObjects
	// If an array is indexed, it creates a list
	// If an array is associative, it creats a DO
	public static function array_to_viewabledata($array) {
		if(is_array($array)) {
			// Figure out whether this is indexed or associative
			$assoc = array_keys($array) !== range(0, count($array) - 1);

			switch($assoc) {
				case true:
					$dataObject = new ArrayData(array());

					foreach($array as $k => $v) {
						if(is_array($v)) {
							$dataObject->setField($k, self::array_to_viewabledata($v));
						} else {
							$dataObject->setField($k, $v);
						}
					}

					return $dataObject;
					break;

				case false:
					$list = new ArrayList();

					foreach($array as $k => $v){
						$list->push(self::array_to_viewabledata($v));
					}

					return $list;
					break;
			}
		}

		return $array;
	}

	

}
