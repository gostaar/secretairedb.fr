<?php
// src/Command/GenerateGoogleTokenCommand.php
namespace App\Command;

use Google_Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:generate-google-token')]
class GenerateGoogleTokenCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $client = new Google_Client();
        $client->setAuthConfig('config/google/credentials.json');
        $client->setScopes(['https://www.googleapis.com/auth/calendar']);
        $client->setAccessType('offline');
        $client->setPrompt('select_account consent');

        $authUrl = $client->createAuthUrl();
        $output->writeln("Ouvre ce lien dans ton navigateur : $authUrl");

        $output->writeln("Entre le code de vérification : ");
        $handle = fopen("php://stdin", "r");
        $authCode = trim(fgets($handle));

        $accessToken = $client->fetchAccessTokenWithAuthCode($authCode);
        fclose($handle);

        if (isset($accessToken['refresh_token'])) {
            $output->writeln("Refresh Token : " . $accessToken['refresh_token']);
        } else {
            $output->writeln("Pas de refresh token trouvé.");
        }

        return Command::SUCCESS;
    }
}
