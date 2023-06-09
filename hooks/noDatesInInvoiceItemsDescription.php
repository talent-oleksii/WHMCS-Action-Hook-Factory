<?php

/**
 * No Dates in Invoice Items Description
 *
 * @package     WHMCS
 * @copyright   Katamaze
 * @link        https://katamaze.com
 * @author      Davide Mantenuto <info@katamaze.com>
 */

use WHMCS\Database\Capsule;

add_hook('InvoiceCreationPreEmail', 1, function($vars) {

    $items = Capsule::table('tblinvoiceitems')->select('id', 'description')->where('invoiceid', '=', $vars['invoiceid'])->get();

    if (!$items) {

        return;
    }

    $dateFormat = Capsule::table('tblconfiguration')->select('value')->where('setting', '=', 'DateFormat')->first();

    if (in_array($dateFormat->value, [ 'DD/MM/YYYY', 'DD.MM.YYYY', 'DD-MM-YYYY' ])) {

        $regex = '/[" "][(](((0[1-9]|[12][0-9]|3[01])[- \/.](0[13578]|1[02])|(0[1-9]|[12][0-9]|30)[- \/.](0[469]|11)|(0[1-9]|1\d|2[0-8])[- \/.]02)[- \/.]\d{4}|29[- \/.]02[- \/.](\d{2}(0[48]|[2468][048]|[13579][26])|([02468][048]|[1359][26])00))[\w -]*[-]*[\w -](((0[1-9]|[12][0-9]|3[01])[- \/.](0[13578]|1[02])|(0[1-9]|[12][0-9]|30)[- \/.](0[469]|11)|(0[1-9]|1\d|2[0-8])[- \/.]02)[- \/.]\d{4}|29[- \/.]02[- \/.](\d{2}(0[48]|[2468][048]|[13579][26])|([02468][048]|[1359][26])00))[)]/';
    }
    elseif ($dateFormat->value == 'MM/DD/YYYY') {

        $regex = '/[" "][(](((0[13578]|1[02])|(0[1-9]|[12][0-9]|30)[-\/.](0[469]|11)|(0[1-9]|1\d|2[0-8])[-\/.]02)[-\/.](0[1-9]|[12][0-9]|3[01])[-\/.]\d{4}|29[-\/.]02[-\/.](\d{2}(0[48]|[2468][048]|[13579][26])|([02468][048]|[1359][26])00))[\w -]*[-]*[\w -](((0[13578]|1[02])|(0[1-9]|[12][0-9]|30)[-\/.](0[469]|11)|(0[1-9]|1\d|2[0-8])[-\/.]02)[-\/.](0[1-9]|[12][0-9]|3[01])[-\/.]\d{4}|29[-\/.]02[-\/.](\d{2}(0[48]|[2468][048]|[13579][26])|([02468][048]|[1359][26])00))[)]/';
    }
    elseif (in_array($dateFormat->value, [ 'YYYY/MM/DD', 'YYYY-MM-DD' ])) {

        $regex = '/[" "][(](\d{4}|29[-\/.]02[-\/.](\d{2}(0[48]|[2468][048]|[13579][26])|([02468][048]|[1359][26])00))[-\/.]((0[13578]|1[02])|(0[1-9]|[12][0-9]|30)[-\/.](0[469]|11)|(0[1-9]|1\d|2[0-8])[-\/.]02)[-\/.](0[1-9]|[12][0-9]|3[01])[\w -]*[-]*[\w -](\d{4}|29[-\/.]02[-\/.](\d{2}(0[48]|[2468][048]|[13579][26])|([02468][048]|[1359][26])00))[-\/.]((0[13578]|1[02])|(0[1-9]|[12][0-9]|30)[-\/.](0[469]|11)|(0[1-9]|1\d|2[0-8])[-\/.]02)[-\/.](0[1-9]|[12][0-9]|3[01])[)]/';
    }

    foreach ($items AS $v) {

        $v->description = preg_replace($regex, '', $v->description);
        Capsule::table('tblinvoiceitems')->where('id', '=', $v->id)->update([ 'description' => $v->description ]);
    }
});
