<!-- start cart  -->
          @php
            $currency = getCurrentCurrency();
            $trial_setting = trialSetting();

            @endphp
   <cart :currency="{{ $currency }}" :trial_setting='@json($trial_setting)'></cart>
<!-- end cart  -->
