<?php
/**
 * @package    Tjsendy
 * @author     Techjoomla <extensions@techjoomla.com>
 * @copyright  Copyright (c) 2009-2020 TechJoomla. All rights reserved.
 * @license    GNU General Public License version 2 or later.
 */

defined('_JEXEC') or die();

use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Factory;

jimport('joomla.event.plugin');

/**
 * Plug-in to save subscriber to sendy list
 *
 * @since  __DEPLOY_VERSION__
 */
class plgRDMediaTjsendy extends JPlugin
{

	protected $autoloadLanguage = true;

	public function onAfterProcessSubscription($data)
	{
		$productCat = $this->getCategory($data['product_id']);
		$user = Factory::getUser($data['userid']);

		foreach ($this->params['field-name'] as $value)
		{
			$lists[] = $value;
		}

		foreach ($lists as $list)
		{
			$array[$list->category] = $list->listid;
		}

		$this->subscribeUser($user,$array[$productCat]);
	}

	function getCategory($productId)
	{
		$db = Factory::getDbo();

		$query = $db->getQuery(true)
			->select('category_id')
			->from($db->quoteName('#__rd_subs_product2category'))
			->where($db->quoteName('product_id') . ' = ' . $db->quote($productId));

		$db->setQuery($query);

		return $db->loadResult();
	}


	public function subscribeUser($user, $listId)
    {
		$http = HttpFactory::getHttp();
		$data = array();
		$data['api_key'] = $this->params['api_key'];
		$data['name'] = $user->name;
		$data['email'] = $user->email;
		$data['list'] = $listId;
		$data['boolean'] = 'true';

		$return = $http->post($this->params['sendy_url'] .'/subscribe', $data);
    }
}
