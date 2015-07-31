<?php

/**
* class to create datalayer-tags for retargeting company Criteo.
* These tags are used by Google Tag Manager.
*
* @author Jeroen Boschman <jeroen@webtastic.nl>
* @version 1.0.0
*
*/
class CriteoTags extends google_tagmanager
{

	/**
	 * create Homepage datalayer-script
	 *
	 * @param array $content with optional data
	 * @return string
	 * @since 1.0.0
	 */
	public function homePage($content=array())
	{

		$datalayer['PageType'] = 'homepage';
		if(!empty($content['email'])) {
			$datalayer['setHashedEmail'] = md5($content['email']);
		}

		$return = "";
		$return .= $this->datalayer_push_clean($datalayer);

		return $return;

	}

	/**
	 * create Listingpage datalayer-script
	 *
	 * @param array $product_impressions shown accommodations
	 * @param array $content with optional data
	 * @return string
	 * @since 1.0.0
	 */
	public function searchAndBook(array $product_impressions, $content=array())
	{

		$datalayer['PageType'] = 'Listingpage';
		if(!empty($content['email'])) {
			$datalayer['setHashedEmail'] = md5($content['email']);
		}

		$datalayer['ProductIDList'] = array();
		foreach ($product_impressions as $key => $value) {
			array_push($datalayer['ProductIDList'], $value['type_id']);

			if(count($datalayer['ProductIDList'])>=10) {
				break;
			}
		}

		$return = "";
		$return .= $this->datalayer_push_clean($datalayer);

		return $return;

	}

	/**
	 * create Productpage datalayer-script
	 *
	 * @param integer $productID
	 * @param array $content with optional data
	 * @return string
	 */
	public function productPage($productID, $content=array())
	{

		$datalayer['PageType'] = 'Productpage';
		if(!empty($content['email'])) {
			$datalayer['setHashedEmail'] = md5($content['email']);
		}
		$datalayer['ProductID'] = intval($productID);

		$return = "";
		$return .= $this->datalayer_push_clean($datalayer);

		return $return;

	}

	/**
	 * create Basketpage datalayer-script
	 *
	 * @param array $gegevens booking data
	 * @param array $content with optional data
	 * @return string
	 * @since 1.0.0
	 */
	public function basketPage(array $gegevens, $content=array())
	{

		$datalayer['PageType'] = 'Basketpage';
		if(!empty($content['email'])) {
			$datalayer['setHashedEmail'] = md5($content['email']);
		}
		$datalayer['ProductBasketProducts'] = 'products_list_javascript_array';

		$extraScript = $this->renderProductsList($gegevens);

		$return = "";
		$return .= $this->datalayer_push_clean($datalayer, $extraScript);

		return $return;

	}

	/**
	 * create Transactionpage datalayer-script
	 *
	 * @param array $gegevens booking data
	 * @param array $content with optional data
	 * @return string
	 * @since 1.0.0
	 */
	public function transactionPage(array $gegevens, $content=array())
	{

		$datalayer['PageType'] = 'Transactionpage';

		$datalayer['setHashedEmail'] = md5($gegevens['stap2']['email']);

		$datalayer['ProductTransactionProducts'] = 'products_list_javascript_array';
		$datalayer['TransactionID'] = $gegevens['stap1']['boekingid'];

		$extraScript = $this->renderProductsList($gegevens);

		$return = "";
		$return .= $this->datalayer_push_clean($datalayer, $extraScript);

		return $return;

	}

	/**
	 * create javaScript-productslist array
	 *
	 * @param array $gegevens booking data
	 * @return string
	 * @since 1.0.0
	 */
	private function renderProductsList(array $gegevens) {

		$sellPrice = null;
		if($gegevens["fin"]["totale_reissom"]>0) {
			$sellPrice = round($gegevens["fin"]["totale_reissom"], 0);
		}

		$productList = array('idProduct'=>$gegevens['stap1']['typeid'], 'sellPrice'=>$sellPrice, 'quantity'=>1);

		$return = "";
		$return .= "var products_list = [];\n";
		$return .= "products_list.push(".json_encode($productList).");\n";

		return $return;

	}
}
