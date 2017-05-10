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
use Behat\Testwork\Hook\Scope\BeforeSuiteScope;
use Behat\Testwork\Hook\Scope\AfterSuiteScope;
use Behat\MinkExtension\Context\RawMinkContext;
use Page\OwncloudPage;
use Page\LoginPage;

require_once 'bootstrap.php';

/**
 * Features context.
 */
class FeatureContext extends RawMinkContext implements Context
{
	private $owncloudPage;
	private $loginPage;
	
	public function __construct(OwncloudPage $owncloudPage, LoginPage $loginPage)
	{
		$this->owncloudPage = $owncloudPage;
		$this->loginPage = $loginPage;
	}
	
	/** @BeforeScenario @AdminLogin*/
	public function setUpScenarioAdminLogin()
	{
		$this->loginPage->open();
		$this->filesPage = $this->loginPage->loginAs("admin", "admin");
		$this->filesPage->waitTillPageIsloaded(10);
	}
	
	/** @BeforeSuite */
	public static function setUpSuite(BeforeSuiteScope $scope)
	{
		$suiteParameters = $scope->getEnvironment()->getSuite()->getSettings() ['context'] ['parameters'];
		$ocPath = rtrim($suiteParameters['ocPath'], '/') . '/';
		
		$result=SetupHelper::createUser($ocPath, "user1", $suiteParameters['regularUserPassword']);
		if ($result["code"] != 0) {
			throw new Exception("could not create user. " . $result["stdOut"] . " " . $result["stdErr"]);
		}
	}
	
	/** @AfterSuite */
	public static function tearDownSuite(AfterSuiteScope $scope)
	{
		$suiteParameters = $scope->getEnvironment()->getSuite()->getSettings() ['context'] ['parameters'];
		$ocPath = rtrim($suiteParameters['ocPath'], '/') . '/';
		
		$result=SetupHelper::deleteUser($ocPath, "user1");
		if ($result["code"] != 0) {
			throw new Exception("could not delete user. " . $result["stdOut"] . " " . $result["stdErr"]);
		}
	}
	
	/**
	 * @Then a notification should be displayed with the text :notificationText
	 */
	public function aNotificationShouldBeDisplayedWithTheText($notificationText)
	{
		PHPUnit_Framework_Assert::assertEquals(
			$notificationText, $this->owncloudPage->getNotificationText()
		);
	}
	
}
