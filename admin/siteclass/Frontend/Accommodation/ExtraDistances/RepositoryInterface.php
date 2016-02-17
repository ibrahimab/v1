<?php
namespace Chalet\Frontend\Accommodation\ExtraDistances;

interface RepositoryInterface
{
	/**
	 * @param integer $id
	 */
	public function all($id);
}