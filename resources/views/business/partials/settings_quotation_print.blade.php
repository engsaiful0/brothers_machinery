<div class="pos-tab-content">
    @php($quotation_settings = $common_settings ?? [])
    <div class="row">
        <div class="col-sm-12"><h4>Quotation Print Branding</h4>
            <p>Primary logo uses the existing Business Logo setting. Leave company text blank to use your existing business and location details.</p>
        </div>
        @foreach([
            'company_name' => 'Company Name', 'address' => 'Company Address', 'mobile' => 'Mobile Number',
            'email' => 'Email Address', 'website' => 'Website'
        ] as $key => $label)
            <div class="col-sm-6">
                <div class="form-group">
                    {!! Form::label('quotation_'.$key, $label.':') !!}
                    {!! Form::text('common_settings[quotation_'.$key.']', $quotation_settings['quotation_'.$key] ?? null, ['class' => 'form-control']) !!}
                </div>
            </div>
        @endforeach
        <div class="clearfix"></div>
        @foreach(['bd_logo' => 'B&D Logo', 'atlas_logo' => 'Atlas Copco Logo', 'footer_image' => 'Quotation Footer Image'] as $key => $label)
            @php($stored_file = $quotation_settings['quotation_'.$key] ?? null)
            <div class="col-sm-4">
                <div class="form-group">
                    {!! Form::label('quotation_'.$key.'_file', $label.':') !!}
                    {!! Form::file('quotation_'.$key.'_file', ['accept' => 'image/*', 'class' => 'form-control']) !!}
                    @if($stored_file && file_exists(public_path('uploads/quotation_branding/'.$stored_file)))
                        <p class="help-block">Current image:</p>
                        <img src="{{ asset('uploads/quotation_branding/'.$stored_file) }}" alt="{{ $label }}" style="max-width: 180px; max-height: 80px; object-fit: contain;">
                        <div class="checkbox">
                            <label><input type="checkbox" name="remove_quotation_{{ $key }}" value="1"> Remove image</label>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
