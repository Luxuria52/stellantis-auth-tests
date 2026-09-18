<?php

declare(strict_types=1);

namespace Stellantis\Tests\Tests\Functional;

use Facebook\WebDriver\Chrome\ChromeOptions;
use Facebook\WebDriver\Remote\DesiredCapabilities;
use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverExpectedCondition;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

final class LoginFormTest extends TestCase
{
    private RemoteWebDriver $browser;
    private string $baseUrl;

    protected function setUp(): void
    {
        $this->baseUrl = rtrim((string) (getenv('BASE_URL') ?: 'https://stellantis.autocrm.ru'), '/');

        $options = new ChromeOptions();
        $options->addArguments([
            '--window-size=1440,900',
            '--disable-dev-shm-usage',
            '--no-sandbox',
            '--disable-blink-features=AutomationControlled',
        ]);
        $options->setExperimentalOption('excludeSwitches', ['enable-automation']);

        $capabilities = DesiredCapabilities::chrome();
        $capabilities->setCapability(ChromeOptions::CAPABILITY, $options);

        $this->browser = RemoteWebDriver::create(
            (string) (getenv('SELENIUM_URL') ?: 'http://localhost:4444/wd/hub'),
            $capabilities,
            10_000,
            10_000,
        );

        $this->openLoginPage();
    }

    protected function tearDown(): void
    {
        if (isset($this->browser)) {
            $this->browser->quit();
        }
    }

    #[Test]
    public function loginWithInvalidCredentialsShowsGenericError(): void
    {
        $this->typeCredentials('qa.invalid@example.com', 'WrongPassword!123');
        $this->submit();

        $error = $this->waitForError();

        self::assertSame('Некорректный email / пароль', trim($error->getText()));
        self::assertSame($this->baseUrl . '/login', strtok($this->browser->getCurrentURL(), '?'));
    }

    #[Test]
    public function emptyFormShowsValidationError(): void
    {
        $this->submit();

        $emailError = $this->browser->wait(10)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(
                WebDriverBy::cssSelector('.field-loginform-email .help-block-error')
            )
        );
        $passwordError = $this->browser->findElement(
            WebDriverBy::cssSelector('.field-loginform-password .help-block-error')
        );

        self::assertSame('Необходимо заполнить «Электронная почта».', trim($emailError->getText()));
        self::assertSame('Необходимо заполнить «Пароль».', trim($passwordError->getText()));
        self::assertSame('', $this->browser->findElement(WebDriverBy::id('loginform-email'))->getAttribute('value'));
        self::assertSame('', $this->browser->findElement(WebDriverBy::id('loginform-password'))->getAttribute('value'));
    }

    #[Test]
    public function passwordIsMasked(): void
    {
        $password = $this->browser->findElement(WebDriverBy::id('loginform-password'));
        $password->sendKeys('VisibleOnlyToTheBrowser123!');

        self::assertSame('password', $password->getAttribute('type'));
        self::assertSame('VisibleOnlyToTheBrowser123!', $password->getAttribute('value'));
    }

    #[Test]
    public function forgotPasswordLinkOpensRecoveryForm(): void
    {
        $this->browser->findElement(WebDriverBy::linkText('Забыли пароль'))->click();

        $this->browser->wait(10)->until(
            WebDriverExpectedCondition::urlContains('/site/restore-password')
        );

        self::assertSame(
            $this->baseUrl . '/site/restore-password',
            strtok($this->browser->getCurrentURL(), '?')
        );
        self::assertStringContainsString('Восстановление пароля', $this->browser->getPageSource());
        self::assertTrue($this->browser->findElement(WebDriverBy::id('restorepasswordform-email'))->isDisplayed());
    }

    private function openLoginPage(): void
    {
        $this->browser->get($this->baseUrl . '/login?language=ru_RU');
        $this->browser->wait(25)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(WebDriverBy::id('loginform-email'))
        );
    }

    private function typeCredentials(string $email, string $password): void
    {
        $this->browser->findElement(WebDriverBy::id('loginform-email'))->sendKeys($email);
        $this->browser->findElement(WebDriverBy::id('loginform-password'))->sendKeys($password);
    }

    private function submit(): void
    {
        $this->browser->findElement(WebDriverBy::cssSelector('button[type="submit"]'))->click();
    }

    private function waitForError(): \Facebook\WebDriver\Remote\RemoteWebElement
    {
        return $this->browser->wait(10)->until(
            WebDriverExpectedCondition::visibilityOfElementLocated(
                WebDriverBy::cssSelector('.field-loginform-password .help-block-error')
            )
        );
    }
}
