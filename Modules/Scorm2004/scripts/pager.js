/*
Requires JQuery
*/

var pager =  
{
	currentPage : null,
	
	Init : function()
	{
		jQuery('table.ilc_page_cont_PageContainer').each(function(ix, el)
		{
			if(pager.currentPage==null)
				pager.currentPage = el;
			else
			{
				jQuery(el).hide();
			}
		}
		);
		jQuery('a.ilc_page_rnavlink_RightNavigationLink').bind("click", function(){pager.NextPage();});
		jQuery('a.ilc_page_lnavlink_LeftNavigationLink').each(function(ix)
			{
				jQuery(this).bind("click", function(){pager.PrevPage();});
				//jQuery(this).hide();
				jQuery(this).css("visibility", "hidden");
			});
		
		pager.updateNextLink();
	},
	
	updateNextLink: function()
	{
		var newPage = jQuery(pager.currentPage).nextAll('table.ilc_page_cont_PageContainer');
		var next_is_final_message = false;
		
		if (newPage.length == 1 && newPage[0].id == "sco_succ_message")
		{
			if(typeof finishSCO == 'function')
			{
				finishSCO();
			}
			if (ilias.questions.determineSuccessStatus() == "failed")
			{
				jQuery('a.ilc_page_rnavlink_RightNavigationLink').hide();
			}
			else
			{
				jQuery('a.ilc_page_rnavlink_RightNavigationLink').show();
			}
		}
		else if (newPage.length==0)
		{
			jQuery('a.ilc_page_rnavlink_RightNavigationLink').hide();
			if(typeof finishSCO == 'function') {
				finishSCO();
			}
		}
		else
		{
			jQuery('a.ilc_page_rnavlink_RightNavigationLink').show();
		}
	},
	
	NextPage : function()
	{
		var newPage = jQuery(pager.currentPage).nextAll('table.ilc_page_cont_PageContainer');
		if(newPage.length>0)
		{
			jQuery(pager.currentPage).hide();
			pager.currentPage = newPage[0];
		
			//fix for IE Bug...imagemap-highlights have to be restored when hidden
			jQuery(pager.currentPage).show("",function(){ jQuery('.imagemap').maphilight({fade:true});});
			ilCOPagePres.fixMarkerPositions();
		
			//jQuery('a.ilc_page_lnavlink_LeftNavigationLink').show();
			jQuery('a.ilc_page_lnavlink_LeftNavigationLink').css("visibility", "");
			pager.updateNextLink();
		}
	},
	
	PrevPage : function()
	{
		var newPage = jQuery(pager.currentPage).prevAll('table.ilc_page_cont_PageContainer');
		if(newPage.length>0)
		{
			jQuery(pager.currentPage).hide();
			pager.currentPage = newPage[0];
			jQuery(pager.currentPage).show("",function(){ jQuery('.imagemap').maphilight({fade:true});});
			ilCOPagePres.fixMarkerPositions();
		
			jQuery('a.ilc_page_rnavlink_RightNavigationLink').show();
			if(newPage.length==1)
			//jQuery('a.ilc_page_lnavlink_LeftNavigationLink').hide();
			jQuery('a.ilc_page_lnavlink_LeftNavigationLink').css("visibility", "hidden");
		}
	},
	
	jumpToElement : function(id)
	{
		var newPage = jQuery("#" + id).parents('table.ilc_page_cont_PageContainer');
		if(newPage.length>0)
		{
			jQuery(pager.currentPage).hide();
			pager.currentPage = newPage[0];
			jQuery(pager.currentPage).show("",function(){ jQuery('.imagemap').maphilight({fade:true});});
			ilCOPagePres.fixMarkerPositions();
		
			var prevPages = jQuery(pager.currentPage).prevAll('table.ilc_page_cont_PageContainer');
			if (prevPages.length == 0)
			{
				jQuery('a.ilc_page_lnavlink_LeftNavigationLink').css("visibility", "hidden");
			}
			else
			{
				jQuery('a.ilc_page_lnavlink_LeftNavigationLink').css("visibility", "");
			}
			pager.updateNextLink();
		}
	}
};

	

 jQuery(document).ready(function(){
   pager.Init();
 });

