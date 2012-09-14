<?php
class Committee extends WS_DynamicGetterSetter
{
	protected $COMMITTEE_CODE;
	protected $SHORT_DESC;
	protected $FULL_DESC;
	
	public function __construct( SimpleXMLElement $xml )
	{
		$class_vars = get_class_vars('Committee');
		foreach ( $class_vars as $key=>$value)
		{
			if( isset($xml->$key ) )
			{
				$this->$key = (string)$xml->$key;
			}
		}			
	}
	
	public function getSHORT_DESC()
	{
		if( isset($this->SHORT_DESC) )
		{
			//$arr = explode(":",$this->SHORT_DESC);
			//return $arr[1];
			return $this->SHORT_DESC;
		}
	}
}
	
?>