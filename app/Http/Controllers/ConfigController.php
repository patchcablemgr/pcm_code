<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConfigController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $filename = '/shared/network_config';
        $contents = file($filename);
        $data = array();

        foreach($contents as $line) {
            $kvp = explode("=", $line);
            $key = trim($kvp[0]);
            $value = rtrim(trim($kvp[1]), " \n\r\t\v\x00");

            switch($key) {
                case 'DHCP':
                    $data['dhcp'] = ($value == 'yes') ? true : false;
                    break;
                case 'HOST':
                    $data['host_address'] = $value;
                    break;
                case 'GATEWAY':
                    $data['gateway'] = $value;
                    break;
                case 'DNS':
                    $data['dns'] = $value;
                    break;
            }
        }

        return $data;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Store a network config to be apply to system.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function networkConfig(Request $request)
    {

        $dhcp = ($request['dhcp']) ? 'yes' : 'no';
        //$dhcp = $request['dhcp'];
        $hostAddress = $request['host_address'];
        $gateway = $request['gateway'];
        $dns = $request['dns'];

        $config = "DHCP=".$dhcp.PHP_EOL;
        $config .= "HOST=".$hostAddress.PHP_EOL;
        $config .= "GATEWAY=".$gateway.PHP_EOL;
        $config .= "DNS=".$dns.PHP_EOL;

        return array('success' => file_put_contents('/shared/network_config', $config));
    }

    /**
     * Store a CSR.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function generateCSR(Request $request)
    {
        $dn = array(
            "countryName" => "US",
            "stateOrProvinceName" => "Washington",
            "localityName" => "Seattle",
            "organizationName" => "PatchCableMgr",
            "organizationalUnitName" => "IT",
            "commonName" => "PCM",
            "emailAddress" => "support@patchcablemgr.com"
        );

        $privkey = openssl_pkey_new(array(
            "private_key_bits" => 2048,
            "private_key_type" => OPENSSL_KEYTYPE_RSA,
        ));

        $csr = openssl_csr_new($dn, $privkey, array('digest_alg' => 'sha256'));
        openssl_csr_export($csr, $csr_string);

        return array('success' => $csr_string);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
