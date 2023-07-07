<?php

  $emailBody = '<table border="0" width="100%" cellpadding="0" cellspacing="0" align="center" style="max-width:600px;margin:auto;border-spacing:0;border-collapse:collapse;background:#f6f6f6;border-radius:0px 0px 10px 10px">
  <tbody>
    <tr style="background-size:cover">
          <td style="text-align:center;border-collapse:collapse;background:#fff;border-radius:10px 10px 0px 0px;color:white;height:50px;background-color:#0a64f9;padding: 10px;">
            <img src="https://ci6.googleusercontent.com/proxy/KMcbu8zrXoyWKSbPbnxVubGTx7PgYRs0S09MuME0p2pHSnUzhBCauFlLKn8LlYdveuxEOkeZehwgsghRc06WBSAvXg=s0-d-e1-ft#https://sandbox.quickvee.com/images/maillogo.png" width="120px">
          </td>
        </tr>
    <tr>
          <td valign="middle" align="center" style="padding-top:25px;padding-bottom:15px;text-align:center;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
            <span style="font-size:20px;color:#2d2d2d">Hello '.$ses_name.'</span>
          </td>
        </tr>        
        <tr>
          <td valign="middle" align="center" style="padding:10px 25px 10px 25px;text-align:center;border-collapse:collapse;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
            <div style="font-size:16px;color:#6d6d6d;font-weight:normal">
              We have received a request to reset the password for your account.
            </div>
          </td>
        </tr>
        <tr>
          <td valign="middle" align="center" style="padding:10px 25px 10px 25px;text-align:center;border-collapse:collapse;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
            <div style="font-size:16px;color:#6d6d6d;font-weight:normal">
              To reset your password, please <a href="'.$code_url.'" target=\"_blank\">click here</a>.
            </div>
          </td>
        </tr>   
        <tr>
          <td valign="middle" align="center" style="padding:10px 25px 25px 25px;text-align:center;border-collapse:collapse;font-family: Roboto,RobotoDraft,Helvetica,Arial,sans-serif;">
            <div style="font-size:13px;color:#6d6d6d;font-weight:normal"><br><br>
               If you did not request your password to be reset, please ignore this email and your password will stay as it is.
            </div>
          </td>
        </tr> 
  </tbody></table>';

?>