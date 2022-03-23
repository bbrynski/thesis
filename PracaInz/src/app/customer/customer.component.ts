import { Component, OnInit } from '@angular/core';
import { CustomerService } from './customer.service';
import { FormCheckService } from '../validators/form-check.service';
import {MessageService} from 'primeng/api';
import {AppRoutingService} from '../app-routing/app-routing.service';
import { Router } from '@angular/router';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { CustomValidators } from '../validators/customValidators';
import { Customer } from '../shared/customer';
import { AccessService } from '../access/access.service';

@Component({
  selector: 'app-customer',
  templateUrl: './customer.component.html',
  styleUrls: ['./customer.component.css']
})
export class CustomerComponent implements OnInit {

  customer: Customer[];
  customerForm: FormGroup;
  adminPanelView: any;
  displayDialogDelete = false;
  idCustomerToDelete: number;
  isEditMode: boolean;
  toEditCustomer: any;
  formErrors = {
    imie: null,
    nazwisko: null,
    telefon: null,
    email: null
  };
  cols: any[];
  isAdmin = false;
  isEmployee = false;
  isOwner = false;

  constructor(private customerService: CustomerService, private formCheckService: FormCheckService,
    private appRoutingService: AppRoutingService, private router: Router, private messageService: MessageService,
    private accessService: AccessService) { }

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
    this.isEditMode = this.customerService.getIsEditMode();
    if (this.isEditMode) {
      this.customerService.changeEditMode();
      this.toEditCustomer = this.customerService.getEditCustomer();
    }
    this.cols = [
      { field: 'Imie', header: 'Imię' },
      { field: 'Nazwisko', header: 'Nazwisko' },
      { field: 'Telefon', header: 'Telefon' },
      { field: 'Email', header: 'Adres e-mail'}
    ];
    this.getAllCustomer();
    this.initCustomerForm();
  }

  initCustomerForm() {
    this.customerForm = new FormGroup({
      id: new FormControl(null),
      imie: new FormControl(null, [Validators.required, CustomValidators.validateAlphanumeric, CustomValidators.validateLength]),
      nazwisko: new FormControl(null, [Validators.required, CustomValidators.validateAlphanumeric, CustomValidators.validateLength]),
      telefon: new FormControl(null, [Validators.required, CustomValidators.validatePhone]),
      email: new FormControl(null, [Validators.required, Validators.email]),
    });
    if (this.isEditMode) {
      this.customerForm.controls['id'].setValue(this.toEditCustomer.Id);
      this.customerForm.controls['imie'].setValue(this.toEditCustomer.Imie);
      this.customerForm.controls['nazwisko'].setValue(this.toEditCustomer.Nazwisko);
      this.customerForm.controls['telefon'].setValue(this.toEditCustomer.Telefon);
      this.customerForm.controls['email'].setValue(this.toEditCustomer.Email);
    }

    this.customerForm.valueChanges.subscribe((data) => {
      this.formErrors = this.formCheckService.validateForm(this.customerForm, this.formErrors, true);
    });
  }

  getAllCustomer(): void {
    this.customerService.getAllCustomer().subscribe(
      data => {
        if (data['customer']) {
          this.customer = data['customer'];
        } else if (data['error']) {
          this.messageService.add({severity: 'error', summary: 'Klient', detail: data['error']});
        }
      },
      error => this.messageService.add({severity: 'error', summary: 'Klient', detail: 'Nie udało się pobrać danych'})
    );
  }

  addCustomer() {
    this.customerService.addCustomer(this.customerForm.value, this.isEditMode).subscribe(
      data => {
        console.log(data);
        if (data['msg']) {
          this.messageService.add({severity: 'success', summary: 'Klient', detail: data['msg']});
           this.isEditMode = false;
          this.initCustomerForm();
        } else  if (data['error'])  {
          this.messageService.add({severity: 'error', summary: 'Klient', detail: data['error']});
        }
      },
      error => {
        console.log(error);
        this.messageService.add({severity: 'error', summary: 'Klient', detail: error});
      }
    );
  }

  editCustomer(index: number) {
    this.customerService.getOneCustomer(index).subscribe(
      data => {
        // this.toEditSnowboard = data[0];
        this.customerService.setEditCustomer(data[0]);
        // console.log(this.toEditSnowboard);
        // console.log(this.toEditSnowboard.Model);
        this.router.navigateByUrl('/zarzadzanie/(adminContent:klient-formularz)');
        // console.log(this.toEditSnowboard);

      },
      error => this.messageService.add({severity: 'error', summary: 'Narty', detail: 'Nie udało się pobrać sprzętu'})
    );
  }

  deleteCustomer(index: number) {
    this.displayDialogDelete = false;
    this.customerService.deleteCustomer(index).subscribe(
      data => {
        console.log(data);
        if (data['msg']) {
          this.messageService.add({severity: 'success', summary: 'Klient', detail: data['msg']});
          this.getAllCustomer();
        } else  if (data['error'])  {
          this.messageService.add({severity: 'error', summary: 'Klient', detail: data['error']});
        }
      },
      error => console.log(error)
    );
  }

  showDialogDelete(indexCustomer) {
    this.displayDialogDelete = true;
    this.idCustomerToDelete = indexCustomer;
  }

}
