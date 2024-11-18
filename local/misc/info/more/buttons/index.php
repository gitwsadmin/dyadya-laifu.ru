<?
require($_SERVER["DOCUMENT_ROOT"]."/bitrix/header.php");
$APPLICATION->SetTitle("Кнопки");
CJSCore::Init('aspro_font-awesome');
?>
<div class="row">
	<div class="col-md-6">
		<h2>Buttons</h2>
		<button type="button" class="btn btn-default mb-10 mr-10">Default</button>
		<button type="button" class="btn btn-primary mb-10 mr-10">Primary</button>
		<button type="button" class="btn btn-success mb-10 mr-10">Success</button>
		<button type="button" class="btn btn-info mb-10 mr-10">Info</button>
		<button type="button" class="btn btn-warning mb-10 mr-10">Warning</button>
		<button type="button" class="btn btn-danger mb-10 mr-10">Danger</button>
		<button type="button" class="btn btn-transparent mb-10 mr-10">Transparent</button>
		<button type="button" class="btn btn-default mb-10 mr-10 btn-transparent-bg">Transparent with border</button>
		<button type="button" class="btn btn-link mb-10 mr-10">Link</button>
		<h2 class="spaced">Buttons Disabled</h2>
		<button type="button" class="btn btn-default mb-10 mr-10 " disabled="disabled">Default Button</button>
		<button type="button" class="btn btn-primary mb-10 mr-10" disabled="disabled">Primary button</button>
		<h2 class="spaced">Buttons with icon</h2>
		<p>
			<br />
			<a class="btn btn-default mb-10 mr-10 btn-xs wc" href=""><i class="fa fa-angle-right"></i><span>All news</span></a>
			<a class="btn btn-default mb-10 mr-10 btn-xs wc" href="" disabled="disabled"><i class="fa fa-angle-right"></i><span>All news</span></a>
			
			<br />
			<a class="btn btn-default mb-10 mr-10 btn-sm wc" href=""><i class="fa fa-angle-right"></i><span>All news</span></a>
			<a class="btn btn-default mb-10 mr-10 btn-sm wc" href="" disabled="disabled"><i class="fa fa-angle-right"></i><span>All news</span></a>
			
			<br/>
			<a class="btn btn-default mb-10 mr-10 wc" href=""><i class="fa fa-check "></i><span>Order project</span></a>
			<a class="btn btn-default mb-10 mr-10 wc" href="" disabled="disabled"><i class="fa fa-check "></i><span>Order project</span></a>
			
			
			<br />
			<a class="btn btn-default mb-10 mr-10 btn-lg wc" href=""><i class="fa fa-angle-right"></i><span>All news</span></a>
			<a class="btn btn-default mb-10 mr-10 btn-lg wc" href="" disabled="disabled"><i class="fa fa-angle-right"></i><span>All news</span></a>
		</p>			
		
	</div>

	<div class="col-md-6">
		<h2>Buttons Sizes</h2>
		<p>
			<button type="button" class="btn btn-default mb-10 mr-10 btn-elg">Extra large button</button>
			<button type="button" class="btn btn-default mb-10 mr-10 btn-elg" disabled="disabled">Extra large button</button>
			<br />
			<button type="button" class="btn btn-default mb-10 mr-10 btn-lg">Large button</button>
			<button type="button" class="btn btn-default mb-10 mr-10 btn-lg" disabled="disabled">Large button</button>
			<br />
			<button type="button" class="btn btn-default mb-10 mr-10">Default button</button>
			<button type="button" class="btn btn-default mb-10 mr-10" disabled="disabled">Default button</button>
			<br />
			<button type="button" class="btn btn-default mb-10 mr-10 btn-md">Mini default button</button>
			<button type="button" class="btn btn-default mb-10 mr-10 btn-md" disabled="disabled">Mini default button</button>
			<br />
			<button type="button" class="btn btn-default mb-10 mr-10 btn-sm">Small button</button>
			<button type="button" class="btn btn-default mb-10 mr-10 btn-sm" disabled="disabled">Small button</button>
			<br />
			<button type="button" class="btn btn-default mb-10 mr-10 btn-xs">Extra small button</button>
			<button type="button" class="btn btn-default mb-10 mr-10 btn-xs" disabled="disabled">Extra small button</button>
		</p>
	</div>
</div>

<div class="row">
	<div class="col-md-4">
		<h4 class="spaced">Inline-btn</h4>
		<p>			
			<a class="btn-inline" href=""><i class="fa fa-angle-right"></i>All news</a> <br/>
			<a class="btn-inline" href="" disabled="disabled">All news<i class="fa fa-angle-right"></i></a>
		</p>
	</div>
	
	<div class="col-md-4">
		<h4 class="spaced">Inline-btn Rounded</h4>
		<p>			
			<a class="btn-inline rounded" href="" disabled="disabled">All news<i class="fa fa-angle-right"></i></a><br/>
			<span class="btn-inline rounded" href="" disabled="disabled">All news<i class="fa fa-angle-right"></i></span>			
		</p>
	</div>
	<div class="col-md-4">
		<h4 class="spaced">inline-btn rounded black</h4>
		<p>			
			<a class="btn-inline black rounded" href=""><i class="fa fa-angle-right"></i>All news</a> <br/>			
			<span class="btn-inline black rounded" href=""><i class="fa fa-angle-right"></i>All news</span><br/>
		</p>
	</div>	
</div>		

<div class="row">
	
	<div class="col-md-4">
		<h4 class="spaced">Inline-btn XS</h4>
		<p>			
			<a class="btn-inline xs" href=""><i class="fa fa-angle-right"></i>btn-inline</a> <br/>
			<a class="btn-inline xs rounded" href=""><i class="fa fa-angle-right"></i>btn-inline rounded</a> <br/>
			<a class="btn-inline xs" href="" disabled="disabled">All news<i class="fa fa-angle-right"></i></a><br/>
			<a class="btn-inline xs rounded" href="" disabled="disabled">All news<i class="fa fa-angle-right"></i></a><br/>
		</p>
	</div>
	
	<div class="col-md-4">
		<h4 class="spaced">Inline-btn SM</h4>
		<p>			
			<a class="btn-inline sm" href=""><i class="fa fa-angle-right"></i>btn-inline</a> <br/>
			<a class="btn-inline sm rounded" href=""><i class="fa fa-angle-right"></i>btn-inline rounded</a> <br/>
			<a class="btn-inline sm" href="" disabled="disabled">All news<i class="fa fa-angle-right"></i></a><br/>
			<a class="btn-inline sm rounded" href="" disabled="disabled">All news<i class="fa fa-angle-right"></i></a><br/>
		</p>
	</div>	
</div>	

<?require($_SERVER["DOCUMENT_ROOT"]."/bitrix/footer.php");?>