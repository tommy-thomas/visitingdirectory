<?php
/**
 * 
 * Value object for Visting Committee object.
 * @author tommyt
 *
 */
class Committee extends WS_DynamicGetterSetter
{
	protected $COMMITTEE_CODE;
	protected $SHORT_DESC;
	protected $FULL_DESC;
	
	public function __construct( $array=array() )
	{
		try {
			if( !empty($array) )
			{
				foreach( $array as $key=>$value)
				{
					if( property_exists($this,$key))
					{
						$this->$key = $value;
					}
				}
			}
		} catch (Exception $e) {
			Application::handleException($e);
		}				
	}
}
	
?>