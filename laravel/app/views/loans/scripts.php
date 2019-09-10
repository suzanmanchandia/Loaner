<script id="kit-template" type="text/x-handlebars-template">
    <fieldset data-index="{{ id }}">
        <legend>Item <span class="count">{{ id }}</span> - Kit <a class="item-close fa fa-times"></a></legend>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-sm-4 loan-options">
                <div class="form-group">
                    <label class="control-label col-sm-3" for="item_{{ id }}_id">Item ID</label>

                    <div class="control-group col-sm-8">
                        <input type="text" name="item[{{ id }}][itemid]" id="item_{{ id }}_id" class="suggest unique-kits form-control" required>
                        <input type="hidden" name="item[{{ id }}][type]" value="kit">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="item_{{ id }}status">Loan Type</label>

                    <div class="control-group col-sm-8">
                        <select name="item[{{ id }}][status]" id="item_{{ id }}status" class="form-control" required>
                            <option value="<?php echo Loan::STATUS_SHORT_TERM; ?>">Short Term</option>
                            <option value="<?php echo Loan::STATUS_LONG_TERM; ?>">Long Term</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="item_{{ id }}loan_length">Length</label>

                    <div class="control-group col-sm-8">
                        <input type="number" name="item[{{ id }}][loan_length]" id="item_{{ id }}loan_length" class="form-control loan-length" required min="1" value="3">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="item_{{ id }}notes">Notes</label>

                    <div class="control-group col-sm-8">
                        <textarea name="item[{{ id }}][notes]" id="item_{{ id }}notes" class="form-control" cols="40" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="col-sm-1 inputs tabbable">
            </div>
            <div class="col-sm-7 equipment">
            </div>
        </div>
    </fieldset>
    <div class="clearfix"></div>
</script>

<script id="equipment-single-template" type="text/x-handlebars-template">
    <div class="col-sm-6 equipment-wrapper bg-{{ class }}" data-id="{{ id }}">
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

<script id="equipment-template" type="text/x-handlebars-template">
    <fieldset data-index="{{ id }}">
        <legend>Item <span class="count">{{ id }}</span> - Equipment <a class="item-close fa fa-times"></a></legend>
        <div class="clearfix"></div>
        <div class="row">
            <div class="col-sm-4 loan-options">
                <div class="form-group">
                    <label class="control-label col-sm-3" for="item_{{ id }}_id">Item ID</label>

                    <div class="control-group col-sm-8">
                        <input type="text" name="item[{{ id }}][itemid]" id="item_{{ id }}_id" class="suggest unique-equipment form-control" required>
                        <input type="hidden" name="item[{{ id }}][type]" value="equipment">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="item_{{ id }}status">Loan Type</label>

                    <div class="control-group col-sm-8">
                        <select name="item[{{ id }}][status]" id="item_{{ id }}status" class="form-control" required>
                            <option value="<?php echo Loan::STATUS_SHORT_TERM; ?>">Short Term</option>
                            <option value="<?php echo Loan::STATUS_LONG_TERM; ?>">Long Term</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="item_{{ id }}loan_length">Length</label>

                    <div class="control-group col-sm-8">
                        <input type="number" name="item[{{ id }}][loan_length]" id="item_{{ id }}loan_length" class="form-control loan-length" required min="1" value="3">
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="item_{{ id }}notes">Notes</label>

                    <div class="control-group col-sm-8">
                        <textarea name="item[{{ id }}][notes]" id="item_{{ id }}notes" class="form-control" cols="40" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="col-sm-1 inputs tabbable">
            </div>
            <div class="col-sm-7 equipment">
            </div>
        </div>
    </fieldset>
    <div class="clearfix"></div>
</script>