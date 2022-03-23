import { Injectable } from '@angular/core';
import { FormGroup } from '@angular/forms';

@Injectable({
  providedIn: 'root'
})
export class FormCheckService {

  temp: any;

  public markFormGroupTouched(formGroup: FormGroup) {
    Object.values(formGroup.controls).forEach(control => {
      control.markAsTouched();
    });
  }
  public validationMessages() {
    const messages = {
      required: 'Pole wymagane.',
      alphanumeric: 'Pole musi zawierać wyłącznie znaki alfanumeryczne.',
      minLength: 'Pole musi zawierać minimum 2 znaki.',
      maxLength: 'Pole nie może zawierać wiecej niż 30 znaków.',
      lengthPattern: 'Format musi zgadzać się ze wzorcem (0.00)',
      pricePattern: 'Niepoprawny format (maksymalnie kwota 2 cyfrowa)',
      amountPattern: 'Ilosć musi być w przedziale od 1 do 999',
      sizeBootsPattern: 'Nieprawidłowy rozmiar buta (20-49.5)',
      email: 'Nieprawidłowy format adresu email',
      phonePattern: 'Nieprawidłowy format numeru telefonu',
      numericPattern: 'Pole może zawierać tylko cyfry'
    };

    return messages;
}

public validateForm(formToValidate: FormGroup, formErrors: any, checkDirty?: boolean) {
  const form = formToValidate;

  for (const field in formErrors) {
    if (field) {
      formErrors[field] = '';
      const control = form.get(field);

      const messages = this.validationMessages();

      if (control && !control.valid) {
        if (!checkDirty || (control.dirty || control.touched)) {
          for (const key in control.errors) {
           if (key) {
              formErrors[field] =  messages[key];
            }
          }
        }
      }
    }
  }
  return formErrors;
}

}
