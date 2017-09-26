<?php
// do everything possible to keep
// script from timing out
ini_set('max_execution_time', 1200);
set_time_limit(1200);
ignore_user_abort(1);
require('../_classes/autoload.php');
/**
 * The Application object.
 */
$app = Application::app();

$committees = array(
    'dc9c6663511c522e5369538a44159693' => 'VCLZ',
    '036d7426484a9670dcd11e33be785eff' => 'VCLY',
    '123f6ed23246ed87b004eb29e46563a6' => 'VCSA',
    'eaa328c81afedc87abe7fff05939e3d4' => 'VVTH',
    'd89f847938a6cf3ea748d02cce8ca5e5' => 'VCGS',
    '92a760250c71c456ddacf10cd587aac5' => 'VVHM',
    'a14aa3d989ef628913d5b3698149b32c' => 'VVLW',
    '4508dea6ececd5a1e92bd5e0c859df3a' => 'VVLB',
    '5e449fe7f4826e9d5e83f973b3708587' => 'VVOI',
    'b81e18ce04724c5bf24d57b5aede8545' => 'VVPS',
    'c1d8003dee0a03b79e3e081881c23196' => 'VCLD',
    'c358905d59da55952d7b9141e3c4926d' => 'VVSS',
    'da38dbd539a4f0d2c4fd80ac9d2d4b50' => 'VSVC',
    '6530b4c19ec6810783eeb724f6a4a3ff' => 'VVIM',
);

$total = 0;

// success or fail message
$message = "";
if (isset($_GET['key'])
    && isset($committees[md5($_GET['key'])])
) {
    try {
        $key = md5($_GET['key']);
        // 1. Committee Code
        $code = $committees[$key];
        // 2. Create curl instance.
        $curl = new cURL(null);
        // 3. Griffin Collection.
        $collection = GriffinCollection::instance($app, $curl);
        // 4. Get authtoken from Griffin to be used in subsequent api calls.
        $curl->authenticate($collection->getLoginUrl());
        $authtoken = array('authtoken' => $curl->__toString());
        // 5. Print, throw, log error.
        if (preg_match("/Authentication failed/i", $curl->__toString()))
        {
            throw new Exception("JOB FAILED: ".GriffinCollection::SERVICE_UNAVAILABLE);
        }

        // 6. Clear out memcached data once for first round to make sure we're getting a new cache.
        if ($key == 'dc9c6663511c522e5369538a44159693')
        {
            // 7. Set and cache array of Committees.
            $collection->clearGriffinCollection();
            sleep(10);

            $collection->setCommittees();
            $collection->setAllMemberData($authtoken);
        }
        // 8. CommitteeMemberManager object that handles xml parsing.
        $manager = new CommitteeMemberManager();
        // 9. Get array of simple xml objects from big payload based on committee code.
        $member_xml = $collection->getMemberData($code, $authtoken);
        if (!empty($member_xml))
        {
            // 10. Get array of CommitteeMember objects.
            $member_list = $manager->load($code, $member_xml)->getCommiteeMemberList();
            // 11. Cache the array.
            $collection->setCachedMemberList($code, $member_list);
            // 12. Flush headers.
            $message .= ++$total . ". ".$collection->getCommitteeName($code). " has been cached.\n";
            ob_flush();
            flush();
        }
        else
        {
            // 13. Throw exception to be caught and added to the output message during prod shop job.
            throw new Exception("JOB FAILED: ".GriffinCollection::EMPTY_DATA);
        }

        // 14. Load all the committees
        foreach ( $committees as $key => $code )
        {
            // 14a. CommitteeMemberManager object that handles xml parsing.
            $manager = new CommitteeMemberManager();
            // 14b. Get array of simple xml objects from big payload based on committee code.
            $member_xml = $collection->getMemberData($code, $authtoken);
            if (!empty($member_xml))
            {
                // 14c. Get array of CommitteeMember objects.
                $member_list = $manager->load($code, $member_xml)->getCommiteeMemberList();
                // 14d. Cache the array.
                $collection->setCachedMemberList($code, $member_list);
                // 14e. Flush headers.
                $message .= ++$total . ". ".$collection->getCommitteeName($code). " has been cached.\n";
                ob_flush();
                flush();
            }
        }

        print $message;
    }
    catch (Exception $e)
    {
        // 15. Let's log, print, and throw error.
        error_log($e->getMessage());
        print $e->getMessage();
    }
}
?>