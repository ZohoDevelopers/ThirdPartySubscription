var SubscriptionsPOC = {

	"Calculate" : function(recurringFee)
	{
		var totalCost = recurringFee;
		var payFrequency = $('#typeToggle').prop("checked");
		var multiplier = payFrequency == true ? 12 : 1;
		$("select[id*='addonSelect_']").each(function()
		{
			var chosenVal = $(this).val();
			var costPerAddon = parseInt(this.id.split("_")[1]);

			var addonPerCostId = "addonPerCost_"+costPerAddon.toString()+"_"+this.id.split("_")[2]+"_"+this.id.split("_")[3];
			var costPerAddon2  = costPerAddon * multiplier;
			$("#"+addonPerCostId).html("$"+costPerAddon2.toString());

			var addonYearMonthId = "addonYearMonth_"+costPerAddon.toString()+"_"+this.id.split("_")[2]+"_"+this.id.split("_")[3];
			$("#"+addonYearMonthId).html("/user/"+(payFrequency == true ? "year" : "month"));

			var specificPriceId = "addonSpecificPrice_"+costPerAddon.toString()+"_"+this.id.split("_")[2]+"_"+this.id.split("_")[3];
			var costOfAddon = chosenVal * costPerAddon2;
			$("#"+specificPriceId).html("$"+costOfAddon.toString());
			totalCost += costOfAddon;

		});
		if(totalCost != 0)
		{
			$("#amountDue").text("Total amount to be paid :$"+totalCost);
			$("#amountDue").show();
		}
		else
		{
			$("#amountDue").hide();
		}
	},

	"UpdateLicenseCount" : function(total,fromId,operation,id)
	{
		if(operation == "+")
		{
			var totalCost = 0;
			var html = "Total Amount to be Paid : ";
			$("select[id*='addUsers_']").each(function()
			{
				var idSplit = this.id.split("_");
				var identifier = idSplit[1]+"_"+idSplit[2];
				if(this.id.includes("_Y"))
				{
					identifier += "_Y";
				}
				var chosenVal = parseInt($(this).val());
				var costSingleAddon = parseInt($("#priceBracket_"+identifier).html());
				var costPerAddon = chosenVal * costSingleAddon;
				if(costPerAddon == 0)
				{
					$("#costPerAddonAddUsers_"+identifier).text("$0");
				}
				else
				{
					$("#costPerAddonAddUsers_"+identifier).text("$"+costPerAddon);
				}
				totalCost += costPerAddon;
			});
			html += "$"+totalCost.toString();
			$("#"+id).html(html);

		}
		else if(operation == "-")
		{
			var selectedLicenses = parseInt($('#'+fromId).find(":selected").val());
			var html = "";
			if(selectedLicenses == 0)
			{
				html = "(Licensed Users: "+total+")";
			}
			else
			{
				html = "(Remaining Licensed Users : "+total+" - "+selectedLicenses+" = "+(total-selectedLicenses)+")";
			}
			$("#"+id).html(html);

		}
	},

	"UpdateSubscription" : function(formId,type)
	{
		SubscriptionsPOC.Freeze(formId);
		var url = "lib/actions/Handler.php";
		var data = {};
		var type,quantity;
		var data = JSON.stringify($("#"+formId).serializeArray());
		var identifier;
		if(formId == "downgradeUserForm")
		{
			identifier = "UpdateSubscriptionDowngrade";
		}
		else if(formId == "upgradeUserForm")
		{
			identifier = "UpdateSubscriptionUpgrade";
		}
		else if(formId == "cancelSubscriptionForm")
		{
			identifier = "CancelSubscription";
		}
		SubscriptionsPOC.MakeAjax(url,"POST",data,identifier);
	},

	"Freeze" : function(formId)
	{
		if(formId == "downgradeUserForm")
		{
			$("#loadingDowngrade").show();
		}
		else if(formId == "upgradeUserForm")
		{
			$("#loadingUpgrade").show();
		}
		else if(formId == "cancelSubscriptionForm")
		{
			$("#loadingCancel").show();
		}
		$(".hideBtn").attr("disabled","disabled");
	},

	"MakeAjax" : function(url,type,data,identifier)
	{
		$.ajax({
		type: type,
		url:url,
		data: {data :JSON.parse(data)},
		success:function(data){
			if(identifier.includes("UpdateSubscription") || identifier.includes("CancelSubscription"))
			{
				if(identifier == "UpdateSubscriptionUpgrade")
				{
					$("#loadingUpgrade").hide();
					$(".upgradepopup").html(data);
				}
				else if(identifier == "UpdateSubscriptionDowngrade")
				{
					$("#loadingDowngrade").hide();
					$(".downgradepopup").html(data);
				}
				else if(identifier == "CancelSubscription")
				{
					$("#loadingCancel").hide();
					$(".cancelpopup").html(data);
				}
				parent.postMessage("UpdateSubscriptionReload","*");
			}
			else if(identifier === "ComputePrice")
			{

			}
		}
		});
	},

	"IsJSON" : function(string)
	{
		try
		{
			var json = JSON.parse(string);
			return true;
		}catch(e)
		{
			return false;
		}
	}



};