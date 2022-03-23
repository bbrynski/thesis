import { FormControl, Validators } from '@angular/forms';

const alphanumeric = /[!@#`~$%^&*()_=+|?><.,;'":]/;
const lengthPattern = /^[1-2]\.\d{2}$/;
const pricePattern = /^[1-9]\d$/;
const amountPattern = /^[1-9]\d{0,2}$/;
const sizeBootsPattern = /^[2-4]{1}[0-9]{1}$|^[2-4]{1}[0-9]{1}\.[0,5]$/;
const minLength = 2;
const maxLength = 30;
const phonePattern = /^[0-9]{9}$/;
const numericPattern = /^[0-9]*$/;

export class CustomValidators extends Validators {

  static validateNumeric(control: FormControl) {
    if (control.value && control.value.length > 0) {
      return !numericPattern.test(control.value) ? { numericPattern: true } : null;
    } else {
      return null;
     }
  }

  static validateAlphanumeric(control: FormControl) {
    if (control.value && control.value.length > 0) {
      return alphanumeric.test(control.value) ? { alphanumeric: true } : null;
    } else {
      return null;
     }
  }

  static validateLength(control: FormControl) {
    if (control.value && control.value.length < minLength) {
      return {minLength: true};
    } else if (control.value && control.value.length >= maxLength) {
      return {maxLength: true};
    }
  }

  static validateLengthPattern(control: FormControl) {
    if (control.value && control.value.length > 0) {
      return !lengthPattern.test(control.value) ? { lengthPattern: true } : null;
    } else {
      return null;
    }
  }

  static validatePricePattern(control: FormControl) {
    if (control.value && control.value.length > 0) {
      return !pricePattern.test(control.value) ? { pricePattern: true } : null;
    } else {
      return null;
    }
  }

  static validateAmountPattern(control: FormControl) {
    if (control.value && control.value.length > 0) {
      return !amountPattern.test(control.value) ? { amountPattern: true } : null;
    } else {
      return null;
    }
  }
  static validateSizeBootsPattern(control: FormControl) {
    if (control.value && control.value.length > 0) {
      return !sizeBootsPattern.test(control.value) ? { sizeBootsPattern: true } : null;
    } else {
      return null;
    }
  }

  static validatePhone(control: FormControl) {
    if (control.value && control.value.length > 0) {
      return !phonePattern.test(control.value) ? { phonePattern: true } : null;
    } else {
      return null;
    }
  }

}
