import { Component, OnInit } from '@angular/core';
import { FormCheckService } from '../validators/form-check.service';
import { MessageService } from 'primeng/api';
import { AppRoutingService } from '../app-routing/app-routing.service';
import { PrincipleService } from '../principle/principle.service';
import { EmployeeService } from './employee.service';

import { Principle } from '../shared/principle';
import { Employee } from '../shared/employee';

import { Router } from '@angular/router';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { CustomValidators } from '../validators/customValidators';

@Component({
  selector: 'app-employee',
  templateUrl: './employee.component.html',
  styleUrls: ['./employee.component.css']
})
export class EmployeeComponent implements OnInit {

  principle: Principle[];
  employee: Employee[];
  employeeForm: FormGroup;
  adminPanelView: any;
  displayDialog = false;
  idEmployeeToDeactivate: number;
  isEditMode: boolean;
  toEditEmployee: any;
  formErrors = {
    imie: null,
    nazwisko: null,
    telefon: null,
    email: null,
    nazwaUzytkownik: null,
    haslo: null,
  };

  constructor(private principleService: PrincipleService, private appRoutingService: AppRoutingService,
    private formCheckService: FormCheckService, private messageService: MessageService,
    private router: Router, private employeeService: EmployeeService) { }

  ngOnInit() {
    this.adminPanelView = this.appRoutingService.checkAdminPanelUrl(this.router.url);
    this.isEditMode = this.employeeService.getIsEditMode();
    if (this.isEditMode) {
      this.employeeService.changeEditMode();
      this.toEditEmployee = this.employeeService.getEditEmployee();
    } else {
      this.getAllPrinciple();
      this.getAllEmployee();
    }
    this.initEmployeeForm();
  }

  initEmployeeForm() {
    if (!this.isEditMode) {
    this.employeeForm = new FormGroup({
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
    this.employeeForm = new FormGroup({
      id: new FormControl(this.toEditEmployee.Id),
      imie: new FormControl(this.toEditEmployee.Imie,
        [Validators.required, CustomValidators.validateAlphanumeric, CustomValidators.validateLength]),
      nazwisko: new FormControl(this.toEditEmployee.Nazwisko,
         [Validators.required, CustomValidators.validateAlphanumeric, CustomValidators.validateLength]),
      telefon: new FormControl(this.toEditEmployee.Telefon, [Validators.required, CustomValidators.validatePhone]),
      email: new FormControl(this.toEditEmployee.Email, [Validators.required, Validators.email]),
      aktywny: new FormControl(this.toEditEmployee.Aktywny)
    });
      /* this.employeeForm.controls['id'].setValue(this.toEditEmployee.Id);
      this.employeeForm.controls['imie'].setValue(this.toEditEmployee.Imie);
      this.employeeForm.controls['nazwisko'].setValue(this.toEditEmployee.Nazwisko);
      this.employeeForm.controls['telefon'].setValue(this.toEditEmployee.Telefon);
      this.employeeForm.controls['email'].setValue(this.toEditEmployee.Email);
    */
    }
    this.employeeForm.valueChanges.subscribe((data) => {
      this.formErrors = this.formCheckService.validateForm(this.employeeForm, this.formErrors, true);
    });
  }

  addEmployee() {
    this.employeeService.addEmployee(this.employeeForm.value, this.isEditMode).subscribe(
      data => {
        console.log(data);
        if (data['msg']) {
          this.messageService.add({severity: 'success', summary: 'Pracownik', detail: data['msg']});
          this.isEditMode = false;
          this.initEmployeeForm();
        } else  if (data['error'])  {
          this.messageService.add({severity: 'error', summary: 'Pracownik', detail: data['error']});
        }
      },
      error => {
        console.log(error);
        this.messageService.add({severity: 'error', summary: 'Pracownik', detail: error});
      }
    );
  }

  getAllEmployee(): void {
    this.employeeService.getAllEmployee().subscribe(
      data => {
        console.log(data);
        if (data['employee']) {
          this.employee = data['employee'];
        } else if (data['error']) {
          this.messageService.add({severity: 'error', summary: 'Pracownik', detail: data['error']});
        }
      },
      error => this.messageService.add({severity: 'error', summary: 'Pracownik', detail: 'Nie udało się pobrać danych'})
    );
  }

  getAllPrinciple(): void {
    this.principleService.getAllPrinciple().subscribe(
      principle => {
        this.principle = principle;
         // if (this.isEditMode) {
         //  this.skiPolesForm.controls['plec'].setValue(gender[this.toEditSkiPolesPack.skiPoles.IdPlec - 1].Id);
       // } else {
          this.employeeForm.controls['prawo'].setValue(principle[0].Id);
       // }
      }
    );
  }

  deactivateEmployee(index: number) {
    this.displayDialog = false;
    this.employeeService.deactivateEmployee(index).subscribe(
      data => {
        console.log(data);
        if (data['msg']) {
          this.messageService.add({severity: 'success', summary: 'Pracownik', detail: data['msg']});
          this.getAllEmployee();
        } else  if (data['error'])  {
          this.messageService.add({severity: 'error', summary: 'Pracownik', detail: data['error']});
        }
      },
      error => console.log(error)
    );
  }

  editEmployee(index: number) {
    this.employeeService.getOneEmployee(index).subscribe(
      data => {
        // this.toEditSnowboard = data[0];
        this.employeeService.setEditEmployee(data[0]);
        // console.log(this.toEditSnowboard);
        // console.log(this.toEditSnowboard.Model);
        this.router.navigateByUrl('/admin/(adminContent:pracownik-formularz)');
        // console.log(this.toEditSnowboard);
      },
      error => this.messageService.add({severity: 'error', summary: 'Pracownik', detail: 'Nie udało się pobrać sprzętu'})
    );
  }

  showDialog(indexEmployee) {
    this.displayDialog = true;
    this.idEmployeeToDeactivate = indexEmployee;
  }

}
