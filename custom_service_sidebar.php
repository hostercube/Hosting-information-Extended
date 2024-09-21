<?php
use WHMCS\View\Menu\Item as MenuItem;
use Illuminate\Database\Capsule\Manager as Capsule;

add_hook('ClientAreaSecondarySidebar', 1, function (MenuItem $secondarySidebar) {
    // Context and credentials fetch.
    $service = Menu::context('service');
    $username = $service->username;
    $serverid = $service->server;
    $domain = $service->domain;
    $password = decrypt($service->password);
    $server = Capsule::table('tblservers')->where('id', '=', $serverid)->value('hostname');
    $ipaddress = Capsule::table('tblservers')->where('id', '=', $serverid)->value('ipaddress');
    $name1 = Capsule::table('tblservers')->where('id', '=', $serverid)->value('nameserver1');
    $name2 = Capsule::table('tblservers')->where('id', '=', $serverid)->value('nameserver2');

    // Display credentials if username is not empty.
    if ($username != '') {
        $secondarySidebar->addChild('credentials', [
            'label' => 'Service Information',
            'uri' => '#',
            'icon' => 'fa-desktop',
        ]);

        $credentialPanel = $secondarySidebar->getChild('credentials');
        $credentialPanel->moveToBack();

        // JavaScript for copying to clipboard with tooltip feedback correction.
        $copyScript = "<script>function copyToClipboard(element, text) { navigator.clipboard.writeText(text).then(() => { const originalTitle = element.getAttribute('title'); element.setAttribute('title', 'Copied!'); element.style.cursor = 'pointer'; setTimeout(() => element.setAttribute('title', originalTitle), 100); }).catch(err => console.error('Copy failed', err)); }</script>";

        // Username and password with copy functionality and tooltip correction.
        $credentialPanel->addChild('username', [
            'label' => '<span title="Click to copy" onclick="copyToClipboard(this, \'' . htmlspecialchars($username) . '\')">' . htmlspecialchars($username) . '</span>' . $copyScript,
            'order' => 1,
            'icon' => 'fa-user',
        ]);
        $credentialPanel->addChild('password', [
            'label' => '<span title="Click to copy" onclick="copyToClipboard(this, \'' . htmlspecialchars($password) . '\')">' . htmlspecialchars($password) . '</span>',
            'order' => 2,
            'icon' => 'fa-lock',
        ]);
        // Domain with hyperlink.
        $credentialPanel->addChild('domain', [
            'label' => '<a href="http://' . htmlspecialchars($domain) . '" target="_blank">' . htmlspecialchars($domain) . '</a>',
            'order' => 3,
            'icon' => 'fa-globe',
        ]);
        // IP Address and servers.
        $credentialPanel->addChild('ip', [
            'label' => htmlspecialchars($ipaddress),
            'order' => 4,
            'icon' => 'fa-info',
        ]);
        $credentialPanel->addChild('server', [
            'label' => htmlspecialchars($server),
            'order' => 5,
            'icon' => 'fa-server',
        ]);
        // Name servers.
        $credentialPanel->addChild('name1', [
            'label' => htmlspecialchars($name1),
            'order' => 6,
            'icon' => 'fa-info-circle',
        ]);
        $credentialPanel->addChild('name2', [
            'label' => htmlspecialchars($name2),
            'order' => 7,
            'icon' => 'fa-info-circle',
        ]);
    }
});
?>
