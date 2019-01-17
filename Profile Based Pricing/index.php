<?php
   include 'lib/APIHelper.php';
   $planObject = "";

   $descriptionMap = array();

   $descriptionMap["Super Admin"] ="Customize Solution,Add or Delete Users,Web App access,Manage Team,Define Process flow";
   $descriptionMap["Dispatcher"] = "Web App access,Create Customer,Schedule Work Orders and Tasks,Create sample Invoices/Estimates,View Work Order status updates";
   $descriptionMap["Field Agents"]="Web App access,Create Customer,Schedule Work Orders and Tasks,Create sample Invoices or Estimates,View Work Order Status updates,Web app access,Manage Team,Define Process flow";

   $subTitle = array();
   $subTitle["Super Admin"] = "Manage users, access the complete solution, and have full customization permissions.";
   $subTitle["Dispatcher"] = "Assign and edit work orders, tasks, and customer details.";
   $subTitle["Field Agents"] = "View work orders, perform field tasks, and mark them as complete.";

   $icons = array();
   $icons["Super Admin"]  =  "superadminicon";
   $icons["Dispatcher"]  =  "dispatchericon";
   $icons["Field Agents"]  =  "fieldagenticon";

   $isCancelled = false;
    $apiHelper = new APIHelper();
    if(isset($_POST['Code']) == 200)
    {      
      $apiHelper->utils->setCustomerData($_POST);
      $appDomain = $apiHelper->utils->getSpecificCustomerData("company_name");
    

        $plansObject = $apiHelper->getAllPlans($appDomain);
         for($planItr=0;$planItr<count($plansObject);$planItr++)
         {
            $currentPlan = $plansObject[$planItr];
            if(strpos($currentPlan["plan_code"],"_Y") == false)
            {
              $planCode = $currentPlan["plan_code"];
              break;
            }
         }

       $hostedPageId = $apiHelper->utils->getSpecificCustomerData("hostedPageId");
       $subscriptionId = "";
       $totalPrice = 0;
       if($hostedPageId != NULL)
       {
         $hostedPageObject = $apiHelper->getHostedpage($hostedPageId);
         $subscriptionObjectFromHostedPageObject = $hostedPageObject["subscription"];
         $subscriptionId = $subscriptionObjectFromHostedPageObject["subscription_id"];
       }
       else
       {
         $subscriptionId = $apiHelper->utils->getSpecificCustomerData("subscription_id");
       }
       $isSubscribed = false;
       if($subscriptionId != NULL)
       {
        $isSubscribed = true;
         $subscriptionObject = $apiHelper->getSubscription($subscriptionId);
         $isCancelled = $subscriptionObject["status"] == "live" ? false : true;
         if($isCancelled)
         {
           $subscriptionObject = NULL;
         }
         else
        {
           $mappingObj = array();
           $addonsArray = $subscriptionObject["addons"];
           for($z=0;$z<count($addonsArray);$z++)
           {
             $currentAddon = $addonsArray[$z];
             $mappingObj[$currentAddon["addon_code"]] = $currentAddon["quantity"];
             $totalPrice += ($currentAddon["price"] * $currentAddon["quantity"]);
           }
           $planCodeFromSubscriptionObject = $subscriptionObject["plan"]["plan_code"];
           if(strpos($planCodeFromSubscriptionObject,"_Y"))
           {
              $planCode .= "_Y";
           }
         }
       }
        $planObject = $apiHelper->getPlan($planCode);
        $addonsObject = $apiHelper->getAddonsForPlan($planCode);
     }
   ?>
<html>
   <head>
      <script type="text/javascript" src="webcontent/js/jquery-3.2.1.min.js"></script>
      <script type="text/javascript" src="webcontent/js/selectbox.js"></script>       
      <link href="webcontent/css/style.css" rel="stylesheet" type="text/css"/>
      <script src="webcontent/js/script.js"></script>
      <script type="text/javascript">
         $(document).ready(function()
         {
            SubscriptionsPOC.Calculate(0);
         });
         window.addEventListener('message', function(event) {
          debugger;
            $("#mainDiv1").hide();
            $(".loadingH").show();
            var json = event['data'];
            if(json != undefined)
            {
             if(json['code'] == 200)
             {
               var data = json.data;
               data.Code = 200;
                 $.ajax({
                 type: "POST",
                 data: data,
                 success:function(data){
                  $(".loadingH").hide();
                  $("#mainDiv1").html(data);
                  $("#mainDiv1").show();
                 }
                 });
             }
            }
         });
         function updateVisibility(id,target)
         {
            var isAtleastOneEnabled = false;
            $("select[name*='"+id+"']").each(function()
            {
              var chosenVal = $(this).val();
              if(chosenVal != 0)
              {
                isAtleastOneEnabled = true;
              }
            });
            if(isAtleastOneEnabled)
            {
              $("#"+target).removeClass("opcity");
            }
            else
            {
              $("#"+target).addClass("opcity");
            }
         }
         $(document).ready(function(){
             var winW = $(window).width();
             var winH = $(window).height();
             $('.section-build').height(winH - 300);
             // Downgrade script Excuite
              
             $('.Downgradeshow').click(function(){
                 $('.downgradepopup-bg').show();
                 // $('.downgradepopup').slideDown(500);
         
             });
               $('.hideBtn').click(function(){
                         $('.downgradepopup-bg').hide();

                     // $('.downgradepopup').slideUp(500, function() {
                     //  }); 
         
             });
           // Downgrade script Excuite
         
         $('.cancelPrompt').click(function(){
                 $('.cancelsub-bg').show();
                 // $('.downgradepopup').slideDown(500);
         
             });
          $('.hideBtn').click(function(){
                        $('.cancelsub-bg').hide();
             });



            // uprade script Excuite
         
             $('.upgradeshow').click(function(){
                 $('.upgradepopup-bg').show();
                 // $('.upgradepopup').slideDown(500);
         
             });
               $('.hideBtn').click(function(){
                         $('.upgradepopup-bg').hide();
                     // $('.upgradepopup').slideUp(500, function() {
                     //  }); 
         
             });
                $('input[type="checkbox"]').click(function(){
                if($(this).is(":checked")){
                  $(this).closest('div').find(".slide_style").css("background-position","-14px -142px"); //no i18n

                  $(".sldmonth").css("opacity",".5");
                  $(".yearlydisbg").css("opacity","1");
                } else{
                  $(this).closest('div').find(".slide_style").css("background-position","-14px -185px"); //no i18n
                     $(".yearlydisbg").css("opacity",".5");
                     $(".sldmonth").css("opacity","1");
                }
                SubscriptionsPOC.Calculate(0);
              });
           // uprade script Excuite
             });
      </script>
   </head>
   <body>
      <div class="loadingH" style="display:none">
            <div class="loaddingDiv">
               <div id="upgradeMsgContent">
                  <div class="mB10">
                     <img class="set fa-spin" src="img/set.png">
                     <img class="set fa-spin-s" src="img/set-s.png">
                  </div>
                     <p class="mT30 f16"><strong>Loading....</strong></p>
                     </div>
                  </div>
         </div>
      <div id="mainDiv1">
         <div class="downgradepopup-bg" style="display: none;">
            <div class="downgradepopup">
               <p class="popuphdrstyle">Downgrade Users</p>
               <div class="labelhdr">
                  <p class="downlabelName">Field Name</p>
                  <p class="downlabelName fR textRT">No of Units</p>
               </div>
               <form id="downgradeUserForm" name="downgradeUserForm">
                <input type="hidden" name="subscriptionId" id="subscriptionId" value="<?php echo $subscriptionId; ?>"/>
                <input type="hidden" name="planCode" id="planCode" value="<?php echo $planObject["plan_code"]; ?>"/>
                <input type="hidden" name="type" id="type" value="DowngradeUsers"/>
              <?php if($subscriptionObject != NULL && $addonsObject != NULL) {
                     for($addonItr=0;$addonItr<count($addonsObject);$addonItr++)
                     {
                       $currentAddonObject = $addonsObject[$addonItr];
                       $addonCode = $currentAddonObject["addon_code"];
                       $priceBrackets = $currentAddonObject["price_brackets"];
                       if(!$mappingObj[$addonCode])
                       {
                        continue;
                       }
                     ?>
               <div class="popuplist">
                  <p class="popupText w35per mT8"><?php echo $currentAddonObject["name"]; ?></p>
                <select class="fR" id="reduceUsers_<?php echo $addonCode; ?>" name="reduceUsers_<?php echo $addonCode; ?>" onchange="SubscriptionsPOC.UpdateLicenseCount('<?php echo $mappingObj[$addonCode];?>','reduceUsers_<?php echo $addonCode; ?>','-','purchasedUsers_<?php echo $addonCode; ?>');updateVisibility('reduceUsers_','downgradeUsers');">
                    <option value="0">Select</option>
                      <?php for($i=1;$i<=$mappingObj[$addonCode]-1;$i++) { ?> 
                              <option value="<?php echo $i; ?>">
                                <?php echo $i ;?>
                              </option>
                              <?php } ?>
                </select>
                <p class="popupText fR textRT mR15 mT8" id="purchasedUsers_<?php echo $addonCode; ?>"><?php echo "(Licensed Users : ".$mappingObj[$addonCode].")"; ?></p>
                <input type="hidden" name="addonCode_<?php echo $addonCode;?>" id="addonCode" value="<?php echo $addonCode; ?>"/>
                <input type="hidden" name="purchasedCount_<?php echo $addonCode; ?>" id="type" value="<?php echo $mappingObj[$addonCode]; ?>"/>
               </div>
              <?php } ?>
              
               <div class="fR mT20">
                  <div class="redBtn opcity fR" id="downgradeUsers" onclick="SubscriptionsPOC.UpdateSubscription('downgradeUserForm','DowngradeUsers')">Downgrade</div>
                  <div class="fR secondaryBtn mR10 hideBtn">Cancel</div>
               </div>
               <div id="loadingDowngrade" style="display: none;clear: both;margin-top: 5px;" class="fR">
               

                   <p>
                <img src="img/ajax-loader1.gif" style="margin-top: 12px;float: left;" />
              <span class="fL" style="margin-top: 20px;margin-left: 10px;">Downgrading ...</span> 
              </p>


              </div>

             <?php } ?>
           </form>
            </div>
         </div>


         <div class="cancelsub-bg" style="display: none;">
            <div class="cancelpopup">
               <div class="popuphdrstyle">
                  <p>Cancel Subscription</p>
                </div>
                <div id="cancelContentDiv">
                  <form id="cancelSubscriptionForm">
                    <input type="hidden" name="subscriptionId" id="subscriptionId" value="<?php echo $subscriptionId; ?>"/>
                <input type="hidden" name="planCode" id="planCode" value="<?php echo $planObject["plan_code"]; ?>"/>
                <input type="hidden" name="type" id="type" value="CancelSubscription"/>
                   <span>Are you sure in Cancelling your Subscription ?</span>
                   <div class="fR mT20" style="width: 100%;">
                  <div class="greenBtn fR" id="cancelSubBtn" onclick="SubscriptionsPOC.UpdateSubscription('cancelSubscriptionForm',undefined)">Yes</div>
                  <div class="fR secondaryBtn mR10 hideBtn">No</div>
                   </div>
                 </form>
                  <div id="loadingCancel" style="display: none;clear: both;margin-top: 5px;" class="fR">
              <p>
                <img src="img/ajax-loader1.gif" style="margin-top: 12px;float: left;" />
              <span class="fL" style="margin-top: 20px;margin-left: 10px;">Cancelling ...</span> 
              </p>
             </div>
                </div>
              </div>
          </div>


         <div class="upgradepopup-bg" style="display: none;">
            <div class="upgradepopup">
               <div class="popuphdrstyle">
                  <p>Upgrade Users</p>
                  <p class="uppopupMandary">Amount calculated on pro-rata basis</p>
               </div>
               <div class="labelhdr">
                  <p class="uplableName">Profile Name</p>
                  <p class="uplableName fL">Pricing</p>
                  <p class="uplableName fL w25per">User Count</p>
                  <p class="uplableName fL w15per">Price</p>
               </div>
               <form id="upgradeUserForm" name="upgradeUserForm">
                <input type="hidden" name="subscriptionId" id="subscriptionId" value="<?php echo $subscriptionId; ?>"/>
                <input type="hidden" name="planCode" id="planCode" value="<?php echo $planObject["plan_code"]; ?>"/>
                <input type="hidden" name="type" id="type" value="UpgradeUsers"/>
                 <?php if($subscriptionObject != NULL && $addonsObject != NULL) {
                     for($addonItr=0;$addonItr<count($addonsObject);$addonItr++)
                     {
                       $currentAddonObject = $addonsObject[$addonItr];
                       $addonCode = $currentAddonObject["addon_code"];
                       $priceBrackets = $currentAddonObject["price_brackets"];
                       if(!$mappingObj[$addonCode])
                       {
                        continue;
                       }
                     ?>
              <input type="hidden" name="purchasedUsers_<?php echo $addonCode;?>" id="purchasedUsers_<?php echo $addonCode; ?>" value="<?php echo $mappingObj[$addonCode]; ?>"/>
               <input type="hidden" name="addonCodeAddUsers_<?php echo $addonCode;?>" id="addonCodeAddUsers_<?php echo $addonCode;?>" value="<?php echo $addonCode; ?>"/>
               <div class="popuplist">
                  <p class="uppopupText mT8"><?php echo $currentAddonObject["name"]; ?></p>
                  <p class="dollerText"><span class="dollerboldText">$<span id="priceBracket_<?php echo $addonCode; ?>"><?php echo $priceBrackets[0]["price"]; ?></span></span>
                  <?php if($subscriptionObject != NULL && strpos($planCode,"_Y")){?>/user/year
                            <?php }else{?>
                            /user/month
                          <?php } ?>
                  </p>
                  <div class="uppopupText w25per">
                  <select id="addUsers_<?php echo $addonCode; ?>" name="addUsers_<?php echo $addonCode; ?>" onchange="SubscriptionsPOC.UpdateLicenseCount('<?php echo $priceBrackets[0]["price"]; ?>','addUsers_<?php echo $addonCode; ?>','+','amountDueAddUsers');updateVisibility('addUsers_','upgradeUsers');">
                    <option value="0">Select</option>
                      <?php for($i=1;$i<=200;$i++) { ?> 
                              <option value="<?php echo $i; ?>">
                                <?php echo $i ;?>
                              </option>
                              <?php } ?>
                </select>
              </div>
                <p class="w15per pricingText" id="costPerAddonAddUsers_<?php echo $addonCode; ?>">$0</p>
               </div>
               <?php } ?>
               <?php if($subscriptionObject != NULL){ ?>
               <p class="total-text" id="amountDueAddUsers">Total amount to be paid : </p>
             <?php } ?>
             
               <div class="fR mT20" style="width: 100%;">
                  <div class="greenBtn opcity fR" id="upgradeUsers" onclick="SubscriptionsPOC.UpdateSubscription('upgradeUserForm','UpgradeUsers')">Upgrade</div>
                  <div class="fR secondaryBtn mR10 hideBtn">Cancel</div>
               </div>
               <div id="loadingUpgrade" style="display: none;clear: both;margin-top: 5px;" class="fR">
              <p>
                <img src="img/ajax-loader1.gif" style="margin-top: 12px;float: left;" />
              <span class="fL" style="margin-top: 20px;margin-left: 10px;">Upgrading ...</span> 
              </p>
             </div>
              <?php } ?>
            </form>
            </div>
         </div>
         <form id="paymentform" method="post" action="lib/actions/Purchase.php">
         <div class="section-build">
            <div class="contenthdrbg">
               <p class="contenthdr">Forthright, <span class="clrHead">profile-based pricing</span></p>
               <?php if ($subscriptionObject != NULL){ ?>
               <div class="fL mT45" style="width: 100%;">
                  <p class="fL clr3" style="font-size:15px; margin-top:18px;">The renewal of your subscription will fall on <?php echo $subscriptionObject["next_billing_at"]; ?>. The subscription amount will be $<?php echo $subscriptionObject["amount"]; ?></p>
                  <div class="fR">
                     <div class="primaryBtn fR upgradeshow">Upgrade Users</div>
                     <div class="secondaryBtn fR mR10 Downgradeshow">Downgrade Users</div>
                    <div class="secondaryBtn fR mR10 cancelPrompt" onclick="">Cancel Subscription</div>
                  </div>
               </div>
               <?php }else{?>
          <div class="fR mT45" style="width: 165px;">
               <p class="sldmonth">Monthly</p>
               <div class="slideDiv"><input type="checkbox" name="typeToggle" id="typeToggle"/><span class="slide_style"></span></div>
                  <p class="slideText">Yearly</p>
            </div>
              <?php } ?>
            </div>
            <div class="tableListbg">
               <table class="tablehdrBg" width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                     <td colspan="2" width="32%">
                        <p class="tle-hdr pL20">Profile</p>
                     </td>
                     <td width="25%">
                        <p class="tle-hdr">Permissions</p>
                     </td>
                     <td width="13%">
                        <p class="tle-hdr">Pricing</p>
                     </td>
                     <td width="15%">
                        <p class="tle-hdr">User Count</p>
                     </td>
                     <td width="15%">
                        <p class="tle-hdr textRT">Aggregrate/profile</p>
                     </td>
                  </tr>
               </table>
                <input type="hidden" name="planCode" value="<?php echo $planObject["plan_code"] ;?>" />
               <table class="table-list" width="100%" border="0" cellpadding="0" cellspacing="0">
                  <?php if($addonsObject != NULL) {
                     for($addonItr=0;$addonItr<count($addonsObject);$addonItr++)
                     {
                       $currentAddonObject = $addonsObject[$addonItr];
                       $addonCode = $currentAddonObject["addon_code"];
                       $priceBrackets = $currentAddonObject["price_brackets"];
                       $addonName = $currentAddonObject["name"];
                       if(strpos($addonCode,"_Y") && $subscriptionObject == NULL)
                       {
                         continue;
                       }
                       if($subscriptionObject != NULL && !$mappingObj[$addonCode])
                       {
                          continue;
                       }
                     ?>
                  <tr>
                     <td width="5%">
                        <div class="tablelist">
                           <div class="profileicon <?php echo $icons[$addonName]; ?>"></div>
                        </div>
                     </td>
                     <td width="27%">
                        <div class="tablelist">
                           <div class="profileContent">
                              <p class="profilehdr"><?php echo $currentAddonObject["name"]; ?></p>
                              <p class="profileText"><?php echo $subTitle[$addonName]; ?></p>
                           </div>
                        </div>
                     </td>
                     <td width="25%">
                        <div class="tablelist">
                          <?php 
                          $description = $descriptionMap[$addonName];
                          $splitDescription = explode(",",$description);
                          for($desc=0;$desc<count($splitDescription);$desc++)
                          {
                           ?>
                           <div class="featurelist">
                            <?php echo $splitDescription[$desc]; ?>
                          </div>
                          <?php 
                         }
                          ?>
                        </div>
                     </td>
                     <td width="13%">
                        <div class="tablelist mT8">
                           <p class="dollerText"><span class="dollerboldText" id="addonPerCost_<?php echo $priceBrackets[0]['price'].'_'.$addonCode; ?>">$<?php echo $priceBrackets[0]["price"]; ?></span><span id="addonYearMonth_<?php echo $priceBrackets[0]['price'].'_'.$addonCode;?>">
                            <?php if($subscriptionObject != NULL && strpos($planCode,"_Y")){?>/user/year
                            <?php }else{?>
                            /user/month
                          <?php } ?>
                          </span></p>
                        </div>
                     </td>
                     <td width="15%">
                        <div class="tablelist mT10">
                          <?php if($subscriptionObject == NULL){ ?>
                           <select name="addonSelect_<?php echo $priceBrackets[0]['price'].'_'.$addonCode; ?>" id="addonSelect_<?php echo $priceBrackets[0]['price'].'_'.$addonCode; ?>" onchange="SubscriptionsPOC.Calculate(<?php echo $planObject["recurring_price"]; ?>,<?php echo $planObject["setup_fee"]; ?>)">
                              <?php for($i=1;$i<=200;$i++) { ?> 
                              <option value="<?php echo $i; ?>">
                                 <?php echo $i ;?>
                              </option>
                              <?php }} ?>
                           </select>
                           <label class="selectLabel">
                            <?php 
                            if($subscriptionObject != NULL)
                            {
                              echo $mappingObj[$addonCode];
                            }
                            ?>
                          users</label>
                        </div>
                     </td>
                     <td width="15%">
                        <div class="tablelist mT10">
                           <div class="amountText" id="addonSpecificPrice_<?php echo $priceBrackets[0]['price'].'_'.$addonCode; ?>">$<?php if($subscriptionObject == NULL){echo $priceBrackets[0]['price'];}else{echo $priceBrackets[0]['price'] * (int)$mappingObj[$addonCode];}?></div>
                        </div>
                     </td>
                  </tr>
                  <?php }} ?>
               </table>
               <?php if($subscriptionObject == NULL){ ?>
              <div class="BtnStyle">
              <input type="submit" class="blackBtn" name="Purchase" value="PROCEED TO PAY"/>
            </div>
            <?php } ?>
             </form>
            </div>
                <p class="total-text" id="amountDue" style="display:none"></p>
   </body>
</html>