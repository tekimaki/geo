{strip}
	<div class="row">
		{formlabel label="latitude" for="geo[lat]"}
		{forminput}
			<input type="text" name="geo[lat]" id="geo_lat" value="" />
		{/forminput}
	</div>

	<div class="row">
		{formlabel label="longetitude" for="geo[lng]"}
		{forminput}
			<input type="text" name="geo[lng]" id="geo_lng" value="" />
		{formhelp note="Location Data"}
		{/forminput}
	</div>
{/strip}