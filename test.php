<?php
class ServerTest extends PHPUnit_Framework_TestCase
{
    /**
     * server pid.
     * @var int
     */
    private $pid;

    /**
     * Port on which server should start.
     * @todo can be moved to test suite configuration
     */
    const PORT = 1234;

    /**
     * Host.
     */
    const HOST = 'localhost';

    /**
     * Folder that is bind to server.
     */
    const SERVER_FOLDER = './';

    /**
     * Sets up new php internal server.
     */
    public function setUp()
    {
        $command = sprintf(
            'php -S %s:%d -t %s >/dev/null 2>&1 & echo $!',
            self::HOST,
            self::PORT,
            self::SERVER_FOLDER
        );

        $output = [];
        exec($command, $output);
        $this->pid = (int) $output[0];

        // without sleep server is not accessible.
        sleep(1);
    }

    /**
     * Kills php server after test run.
     */
    public function tearDown()
    {
        if ($this->pid)
        {
            exec('kill ' . $this->pid);
        }
    }

    /**
     * Tests server response with empty request.
     */
    public function testNoParam()
    {
        $response = $this->getHeaders('server.php');

        $this->assertFalse(strpos($response, 'My-Header'), 'Response should not contain My-Header');
    }

    /**
     * Tests server response with p parameter in request.
     */
    public function testParam()
    {
        $parameter = 1;
        $response = $this->getHeaders('server.php?p=' . $parameter);
        $this->assertTrue(strpos($response, 'My-Header: ' . $parameter) !== false, 'Header not found!');
    }

    /**
     * Returns response header.
     *
     * @param string $url
     * @return string
     */
    private function getHeaders($url)
    {
        $ch = curl_init('http://localhost:' . self::PORT . '/' . $url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $response = curl_exec($ch);
        return $response;
    }
}