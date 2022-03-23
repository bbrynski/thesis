import { Component, OnInit } from '@angular/core';
import { FormCheckService } from '../validators/form-check.service';
import { MessageService, SelectItem } from 'primeng/api';
import { AppRoutingService } from '../app-routing/app-routing.service';
import { PrincipleService } from '../principle/principle.service';
import { UserService } from './user.service';
import { AccessService } from '../access/access.service';

import { Principle } from '../shared/principle';
import { User } from '../shared/user';

import { Router } from '@angular/router';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { CustomValidators } from '../validators/customValidators';
@Component({
  selector: 'app-user',
  templateUrl: './user.component.html',
  styleUrls: ['./user.component.css']
})
export class UserComponent implements OnInit {

  principle: Principle[];
  user: User[];
  userForm: FormGroup;
  adminPanelView: any;
  displayDialog = false;
  idUserToDeactivate: number;
  isEditMode: boolean;
  toEditUser: any;
  formErrors = {
    imie: null,
    nazwisko: null,
    telefon: null,
    email: null,
    nazwaUzytkownik: null,
    haslo: null,
  };

  cols: any[];
  isAdmin = false;
  isEmployee = false;
  isOwner = false;
  select: SelectItem[];


  constructor(private principleService: PrincipleService, private appRoutingService: AppRoutingService,
    private formCheckService: FormCheckService, private messageService: MessageService,
    private router: Router, private userService: UserService, private accessService: AccessService) { }

  ngOnInit() {

    this.accessService.isAdmin.subscribe(data => {
      this.isAdmin = data;
    });
    this.accessService.isEmployee.subscribe(data => {
      this.isEmployee = data;
    });
    this.accessService.isOwner.subscribe(data => {
      this.isOwner = data;
    });

    this.adminPanelView = this.appRoutingService.checkAdminPanelUrl(this.router.url);
    this.isEditMode = this.userService.getIsEditMode();
    if (this.isEditMode) {
      this.userService.changeEditMode();
      this.toEditUser = this.userService.getEditEmployee();
    }

    this.cols = [
      { field: 'Imie', header: 'Imię' },
      { field: 'Nazwisko', header: 'Nazwisko' },
      { field: 'Telefon', header: 'Telefon' },
      { field: 'Email', header: 'Adres e-mail'},
      { field: 'NazwaUzytkownik', header: 'Nazwa'},
      { field: 'NazwaPrawo', header: 'Uprawnienie'}
    ];

    this.select = [
      { label: 'brak filtru', value: null },
      { label: 'Administrator', value: 'Administrator' },
      { label: 'Właściciel', value: 'Właściciel' },
      { label: 'Pracownik', value: 'Pracownik' }
  ];

    this.initUserForm();
    this.getAllPrinciple();
    this.getAllUser();
  }

  initUserForm() {
    if (!this.isEditMode) {
    this.userForm = new FormGroup({
      id: new FormControl(null),
      imie: new FormControl(null, [Validators.required, CustomValidators.validateAlphanumeric, CustomValidators.validateLength]),
      nazwisko: new FormControl(null, [Validators.required, CustomValidators.validateAlphanumeric, CustomValidators.validateLength]),
      telefon: new FormControl(null, [Validators.required, CustomValidators.validatePhone]),
      email: new FormControl(null, [Validators.required, Validators.email]),
      nazwaUzytkownik: new FormControl(null, [Validators.required, CustomValidators.validateAlphanumeric,
         CustomValidators.validateLength]),
      haslo: new FormControl(null),
      prawo: new FormControl(null, Validators.required),
    });
  } else {
    this.userForm = new FormGroup({
      id: new FormControl(this.toEditUser.Id),
      imie: new FormControl(this.toEditUser.Imie,
        [Validators.required, CustomValidators.validateAlphanumeric, CustomValidators.validateLength]),
      nazwisko: new FormControl(this.toEditUser.Nazwisko,
         [Validators.required, CustomValidators.validateAlphanumeric, CustomValidators.validateLength]),
      telefon: new FormControl(this.toEditUser.Telefon, [Validators.required, CustomValidators.validatePhone]),
      email: new FormControl(this.toEditUser.Email, [Validators.required, Validators.email]),
      nazwaUzytkownik: new FormControl(this.toEditUser.NazwaUzytkownik, [Validators.required, CustomValidators.validateAlphanumeric,
        CustomValidators.validateLength]),
     haslo: new FormControl(this.toEditUser.Haslo),
     prawo: new FormControl(null, Validators.required),
      aktywny: new FormControl(this.toEditUser.Aktywny)
    });
      /* this.employeeForm.controls['id'].setValue(this.toEditEmployee.Id);
      this.employeeForm.controls['imie'].setValue(this.toEditEmployee.Imie);
      this.employeeForm.controls['nazwisko'].setValue(this.toEditEmployee.Nazwisko);
      this.employeeForm.controls['telefon'].setValue(this.toEditEmployee.Telefon);
      this.employeeForm.controls['email'].setValue(this.toEditEmployee.Email);
    */
    }
    this.userForm.valueChanges.subscribe((data) => {
      this.formErrors = this.formCheckService.validateForm(this.userForm, this.formErrors, true);
    });
  }

  getAllUser(): void {
    this.userService.getAllUser().subscribe(
      data => {
        console.log(data);
        if (data['user']) {
          this.user = data['user'];
        } else if (data['error']) {
          this.messageService.add({severity: 'error', summary: 'Uzytkownik', detail: data['error']});
        }
      },
      error => this.messageService.add({severity: 'error', summary: 'Uzytkownik', detail: 'Nie udało się pobrać danych'})
    );
  }

  getAllPrinciple(): void {
    this.principleService.getAllPrinciple().subscribe(
      principle => {
        this.principle = principle;
          if (this.isEditMode) {
           this.userForm.controls['prawo'].setValue(principle[this.toEditUser.IdPrawo - 1].Id);
        } else {
           this.userForm.controls['prawo'].setValue(principle[0].Id);
        }
      }
    );
  }

  addUser() {
    this.userService.addUser(this.userForm.value, this.isEditMode).subscribe(
      data => {
        console.log(data);
        if (data['msg']) {
          this.messageService.add({severity: 'success', summary: 'Uzytkownik', detail: data['msg']});
          this.isEditMode = false;
          this.initUserForm();
        } else  if (data['error'])  {
          this.messageService.add({severity: 'error', summary: 'Uzytkownik', detail: data['error']});
        }
      },
      error => {
        console.log(error);
        this.messageService.add({severity: 'error', summary: 'Uzytkownik', detail: error});
      }
    );
  }

  showDialog(index: number) {
    this.displayDialog = true;
    this.idUserToDeactivate = index;
  }

  editUser(index: number) {
    this.userService.getOneUser(index).subscribe(
      data => {
        // this.toEditSnowboard = data[0];
        console.log(data['user'][0]);
        this.userService.setEditUser(data['user'][0]);
        // console.log(this.toEditSnowboard);
        // console.log(this.toEditSnowboard.Model);
        this.router.navigateByUrl('/zarzadzanie/(adminContent:uzytkownik-formularz)');
        // console.log(this.toEditSnowboard);
      },
      error => this.messageService.add({severity: 'error', summary: 'Uzytkownik', detail: 'Błąd'})
    );
  }

  deactivateUser(index: number) {
    this.displayDialog = false;
    this.userService.deactivateUser(index).subscribe(
      data => {
        console.log(data);
        if (data['msg']) {
          this.messageService.add({severity: 'success', summary: 'Uzytkownik', detail: data['msg']});
          this.getAllUser();
        } else  if (data['error'])  {
          this.messageService.add({severity: 'error', summary: 'Uzytkownik', detail: data['error']});
        }
      },
      error => console.log(error)
    );
  }

}
