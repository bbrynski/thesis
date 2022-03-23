import { Component, OnInit, Inject } from '@angular/core';
import { FormGroup, FormControl, Validators } from '@angular/forms';
import { FormCheckService } from '../validators/form-check.service';
import { AccessService } from './access.service';
import {MessageService} from 'primeng/api';
import { LOCAL_STORAGE, StorageService } from 'angular-webstorage-service';
import { Router } from '@angular/router';

@Component({
  selector: 'app-access',
  templateUrl: './access.component.html',
  styleUrls: ['./access.component.css']
})
export class AccessComponent implements OnInit {

  loginForm: FormGroup;
  formErrors = {
    login: null,
    password: null,
  };

  constructor(private formCheckService: FormCheckService, private accessService: AccessService,
    private messageService: MessageService, @Inject(LOCAL_STORAGE) private localStorage: StorageService,
    private router: Router) { }

  ngOnInit() {
    this.initLoginForm();
  }

  initLoginForm() {
    this.loginForm = new FormGroup({
      login: new FormControl(null, Validators.required),
      password: new FormControl(null, Validators.required),
      recaptcha: new FormControl(null, [Validators.required])
    });

    this.loginForm.valueChanges.subscribe((data) => {
      this.formErrors = this.formCheckService.validateForm(this.loginForm, this.formErrors, true);
    });
  }

  login() {
    this.accessService.login(this.loginForm.value).subscribe(
      data => {
        console.log(data);
        if (data['token']) {
          this.localStorage.set('currentUser', data);
          this.messageService.add({severity: 'success', summary: 'Logowanie', detail: 'Poprawnie zalogowano'});
          this.accessService.checkLocalStorage();
          this.router.navigateByUrl('/');
        }
        if (data['error']) {
          this.messageService.add({severity: 'error', summary: 'Logowanie', detail: data['error']});
        }
      },
      error => {

      }
    );
  }

}
