<?php


use JsonApi\Errors\AuthorizationFailedException;
use JsonApi\Routes\Users\UsersIndex;

class UsersIndexTest extends \Codeception\Test\Unit
{
    /**
     * @var \UnitTester
     */
    protected $tester;

    protected function _before()
    {
        \DBManager::getInstance()->setConnection('studip', $this->getModule('\\Helper\\StudipDb')->dbh);
    }

    protected function _after()
    {
    }

    // tests

    public function testIndexUsers()
    {
        $credentials = $this->tester->getCredentialsForTestAutor();
        $response = $this->getUsers($credentials);
        $this->tester->assertTrue($response->isSuccessfulDocument([200]));

        $vis_query = get_vis_query(context: 'search');
        $condition = "LEFT JOIN user_visibility ON (user_visibility.user_id = auth_user_md5.user_id) WHERE {$vis_query}";
        $numberOfAllUsers = \User::countBySQL($condition);
        $this->tester->assertSame($numberOfAllUsers, count($response->document()->primaryResources()));

        $this->assertValidResourceObject($response, 'users');

        $this->tester->storeJsonMd('get_users', $response, 1, '[...]');
    }

    // **** helper functions ****
    private function getUsers($credentials)
    {
        $app = $this->tester->createApp($credentials, 'get', '/users', UsersIndex::class);

        return $this->tester->sendMockRequest(
            $app,
            $this->tester->createRequestBuilder($credentials)
            ->setUri('/users')
            ->fetch()
            ->getRequest()
        );
    }

    public function assertValidResourceObject($response, $type)
    {
        $this->tester->assertSame($type, $response->document()->primaryResources()[0]->type());
    }
}
