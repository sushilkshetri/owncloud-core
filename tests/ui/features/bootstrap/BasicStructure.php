<?php
/**
 * ownCloud
*
* @author Artur Neumann
* @copyright 2017 Artur Neumann info@individual-it.net
*
* This library is free software; you can redistribute it and/or
* modify it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE
* License as published by the Free Software Foundation; either
* version 3 of the License, or any later version.
*
* This library is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
* GNU AFFERO GENERAL PUBLIC LICENSE for more details.
*
* You should have received a copy of the GNU Affero General Public
* License along with this library.  If not, see <http://www.gnu.org/licenses/>.
*
*/

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Page\OwncloudPage;
use Page\LoginPage;

require_once 'bootstrap.php';

/**
 * Features context.
 */
trait BasicStructure
{
	private $owncloudPage;
	private $loginPage;
	private $regularUserPassword;
	private $regularUserNames = array();
	private $ocPath;

	/** @BeforeScenario @AdminLogin*/
	public function setUpScenarioAdminLogin()
	{
		$this->loginPage->open();
		$this->filesPage = $this->loginPage->loginAs("admin", "admin");
		$this->filesPage->waitTillPageIsloaded(10);
	}

	/** @BeforeScenario @CreateRegularUsers */
	public function setUpScenarioCreateRegularUsers(BeforeScenarioScope $scope)
	{
		$suiteParameters = $scope->getEnvironment()->getSuite()->getSettings() ['context'] ['parameters'];
		$users = explode(",", $suiteParameters['regularUserNames']);
		$this->ocPath = rtrim($suiteParameters['ocPath'], '/') . '/';

		$this->regularUserPassword = $suiteParameters['regularUserPassword'];
		foreach ($users as $user) {
			$user = trim($user);
			$result=SetupHelper::createUser($this->ocPath, $user, $this->regularUserPassword);
			if ($result["code"] != 0) {
				throw new Exception("could not create user. " . $result["stdOut"] . " " . $result["stdErr"]);
			}
			array_push($this->regularUserNames, $user);
		}
	}

	/** @AfterScenario @CreateRegularUsers */
	public function tearDownScenarioCreateRegularUsers(AfterScenarioScope $scope)
	{
		foreach ($this->regularUserNames as $user) {
			$result=SetupHelper::deleteUser($this->ocPath, $user);
			if ($result["code"] != 0) {
				throw new Exception("could not delete user. " . $result["stdOut"] . " " . $result["stdErr"]);
			}
		}
	}

	public function getRegularUserPassword ()
	{
		return $this->regularUserPassword;
	}

	public function getRegularUserNames ()
	{
		return $this->regularUserNames;
	}
}
