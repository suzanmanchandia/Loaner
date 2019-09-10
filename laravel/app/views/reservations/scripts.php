
<script id="equipment-template" type="text/x-handlebars-template">
    <div class="col-sm-3 equipment-wrapper bg-{{ class }}" data-id="{{ id }}">
        <div class="equipment-actions">
            <span class="fa fa-times" data-toggle="tooltip" title="Missing Item"></span><br>
            <span class="fa fa-wrench" data-toggle="tooltip" title="Broken Item"></span>
        </div>
        <div class="row">
            <strong class="col-xs-5">Equipment ID</strong>
            <span class="col-xs-7">{{ id }}</span>
            <strong class="col-xs-3">Model</strong>
            <span class="col-xs-9">{{ model }}</span>
            <strong class="col-xs-3">Condition</strong>
            <span class="col-xs-9 condition">{{ condition }}</span>
            <input type="hidden" name="equipment[{{ id }}]" value="{{ condition }}">
        </div>
    </div>
</script>