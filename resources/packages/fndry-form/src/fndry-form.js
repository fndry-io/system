import FndryFormSchema from './components/FormSchema';
import FndryFormType from './components/FormType';
import FndryFormGroup from './components/FormGroup';
import FndryFormButtons from './components/FormButtons';

import VeeValidate, { Validator } from 'vee-validate';

Validator.extend('nullable', {
    getMessage: field => 'The ' + field + ' value can\'t be empty.',
    validate: value => true

});

Validator.extend('in', {
    getMessage: field => 'The ' + field + ' value is not in the allowed values.',
    validate: (value, args) => {
        for (let i in args) {
            //todo figure out a better way than eval
            if (value == eval(args[i])) {
                return true;
            }
        }
        return false;
    }
});

export {
    FndryFormSchema,
    FndryFormGroup,
    FndryFormType,
    FndryFormButtons,
};

/**
 * FndryForm
 * (c) 2019
 * @license MIT
 */

const FndryForm = {};

/**
 * Plugin API
 */
FndryForm.install = function (Vue, options) {
    Vue.component('fndry-form-schema', FndryFormSchema);
    Vue.component('fndry-form-type', FndryFormType);
    //Vue.component('fndry-form-group', FndryFormGroup);

    Vue.use(VeeValidate, {
        // This is the default
        inject: true,
        // Important to name this something other than 'fields'
        fieldsBagName: 'veeFields',
        // This is not required but avoids possible naming conflicts
        errorBagName: 'veeErrors'
    });

};


/**
 * Auto install
 */
if (typeof window !== 'undefined' && window.Vue) {
    window.Vue.use(FndryForm)
}

export default FndryForm