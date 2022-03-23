import { Component, OnInit, Inject } from '@angular/core';
import { SESSION_STORAGE, StorageService } from 'angular-webstorage-service';

import { Snowboard } from '../shared/snowboard';
import { Skis } from '../shared/skis';
import { SnowboardLeg } from '../shared/snowboard-leg';
import { SkiPoles } from '../shared/ski-poles';
import { Boots } from '../shared/boots';

import { SnowboardService} from '../snowboard/snowboard.service';
import { SkisService} from '../skis/skis.service';
import { SkiPolesService } from '../ski-poles/ski-poles.service';
import { BootsService } from '../boots/boots.service';
import { ReservationService } from './reservation.service';
import { AccessService } from '../access/access.service';
import { CustomerService } from '../customer/customer.service';
import { RentService } from '../rent/rent.service';

import { FormControl, FormGroup, Validators } from '@angular/forms';
import { CustomValidators } from '../validators/customValidators';
import { FormCheckService } from '../validators/form-check.service';

import {MessageService, SelectItem} from 'primeng/api';
import { Router } from '@angular/router';
import {Observable} from 'rxjs';
import {map, startWith} from 'rxjs/operators';

@Component({
  selector: 'app-reservation',
  templateUrl: './reservation.component.html',
  styleUrls: ['./reservation.component.css']
})
export class ReservationComponent implements OnInit {

  dateFrom: any;
  dateTo: Date;
  minDateValue;

  occupiedDatesArraySnowboard: any[];
  occupiedDatesArraySkis: any[];
  occupiedDatesArraySkiPoles: any[];
  occupiedDatesArrayBoots: any[];

  availableDatesArraySnowboard: any[];
  availableDatesArraySkis: any[];
  availableDatesArraySkiPoles: any[];
  availableDatesArrayBoots: any[];

  messageSnowboardError: boolean;
  messageSkisError: boolean;
  messageSkiPolesError: boolean;
  messageBootsError: boolean;

  errorReservation: boolean;
  reservationData = {
    customer: null,
    snowboardOptions: null,
    skisOptions: null,
    boots: null,
    skiPoles: null,
    dateFrom: null,
    dateTo: null
  };

  snowboardOptionsForm: FormGroup;
  skisOptionsForm: FormGroup;
  customerForm: FormGroup;
  reservationForm: FormGroup;

  snowboardLeg: SnowboardLeg[];

  snowboard: Snowboard;
  skis: Skis;
  skiPoles: SkiPoles;
  boots: Boots;

  formErrorsCustomer = {
    imie: null,
    nazwisko: null,
    telefon: null,
    email: null
  };

  formErrorsSnowboardOptions = {
    katL: null,
    katP: null
  };
  formErrorsSkisOptions = {
    waga: null,
    rozmiarButa: null
  };

  isEmployee: boolean;
  isAdmin: boolean;
  isOwner: boolean;
  employeeView: boolean;

  reservations: any[];
  displayDialogCancel = false;

  displayDialogAddEquipment = false;
  displayOptionsEquipment = false;
  displaySkisOptions = false;
  displaySnowboardOptions = false;
  selectedEquipmentType = null;

  cancelReservationId: number;
  customerId: number;

  rentEquipment: number[] = [];
  possibleMultipleRent: number[] = [];

  snowboardList: any[];
  skisList: any[];
  skiPolesList: any[];
  bootsList: any[];
  snowboardFormControl = new FormControl();
  skisFormControl = new FormControl();
  skiPolesFormControl = new FormControl();
  bootsFormControl = new FormControl();
  pl: any;

  selectedPeriod: string;
  selectedStatus: string;
  periodList: any[];
  statusList: any[];
  cols: any[];
  selectStatus: SelectItem[];
  optionsSkis: any;
  optionsSnowboard: any;

  constructor(@Inject(SESSION_STORAGE) private sessionStorage: StorageService, private snowboardService: SnowboardService,
  private skisService: SkisService, private formCheckService: FormCheckService, private messageService: MessageService,
  private reservationService: ReservationService, private skiPolesService: SkiPolesService,
  private bootsService: BootsService, private accessService: AccessService, private router: Router,
  private customerService: CustomerService, private rentService: RentService) { }

  ngOnInit() {
    this.selectedPeriod = 'today';
    this.selectedStatus = 'active';
    this.initLanguageCalendar();
    this.minDateValue = new Date();
    this.dateFrom = new Date();
    this.dateTo = null;

    this.accessService.isEmployee.subscribe(data => {
      this.isEmployee = data;
    });
    this.accessService.isAdmin.subscribe(data => {
      this.isAdmin = data;
    });
    this.accessService.isOwner.subscribe(data => {
      this.isOwner = data;
    });

    if ((this.isEmployee || this.isAdmin || this.isOwner) && /(adminContent:rezerwacja-lista)/.test(this.router.url)) {
      this.employeeView = true;
      this.getAllReservation();
      this.getAllSnowboard(true);
      this.getAllSkis(true);
      this.getAllSkiPoles(true);
      this.getAllBoots(true);
      this.selectStatus = [
        { label: 'brak filtru', value: null },
        { label: 'Zarezerwowane', value: 'Zarezerwowane' },
        { label: 'Przygotowane', value: 'Przygotowane' },
        { label: 'Zakończone', value: 'Zakończone' }
    ];
    } else {
      this.employeeView = false;
      this.getAllSnowboard(false);
      this.getAllSkis(false);
      this.getAllSkiPoles(false);
      this.getAllBoots(false);
    }

    this.cols = [
      { field: 'DataOd', header: 'Data wypożyczenia' },
      { field: 'Nazwisko', header: 'Klient' },
      { field: 'Sprzet', header: 'Sprzęt' },
      { field: 'NazwaStatus', header: 'Stauts' }
  ];

    this.initPeriodList();
    this.initStatusList();
    this.initCustomerForm();
    this.initReservationForm();


  }



  initPeriodList() {
    this.periodList = [
      {label: 'Dziś', value: 'today'},
      {label: 'Jutro', value: 'tomorrow'},
      {label: 'Wszystkie', value: 'all'}
    ];
    this.selectedPeriod = 'today';
  }

  initStatusList() {
    this.statusList = [
      {label: 'Aktywne', value: 'active'},
      {label: 'Zakończone', value: 'ended'},
      {label: 'Wszystkie', value: 'all'}
    ];
    this.selectedStatus = 'active';
  }

  initLanguageCalendar() {
    this.pl = {
      firstDayOfWeek: 1,
      dayNames: ['Niedziela', 'Poniedziałek', 'Wtorek', 'Środa', 'Czwartek', 'Piątek', 'Sobota'],
      dayNamesShort: ['Niedz', 'Pon', 'Wt', 'Śr', 'Czw', 'Pt', 'Sob'],
      dayNamesMin: ['Niedz', 'Pon', 'Wt', 'Śr', 'Czw', 'Pt', 'Sob'],
      monthNames: [ 'Styczeń', 'Luty', 'Marzec', 'Kwiecień', 'Maj', 'Czerwiec', 'Lipiec', 'Sierpień', 'Wrzesień',
       'Październik', 'Listopad', 'Grudzień'],
      monthNamesShort: [ 'Sty', 'Lut', 'Mar', 'Kwi', 'Maj', 'Cze', 'Lip', 'Sie', 'Wrz', 'Paź', 'Lis', 'Gru' ],
      today: 'Dzisiaj',
      clear: 'Wyczyść',
      dateFormat: 'dd/mm/yy'
    };
  }

  displayOptions () {
    this.displayOptionsEquipment = true;
  }

  clearAllMessages() {
    this.occupiedDatesArraySnowboard = [];
    this.occupiedDatesArraySkis = [];
    this.occupiedDatesArraySkiPoles = [];
    this.occupiedDatesArrayBoots = [];

    this.availableDatesArraySnowboard = [];
    this.availableDatesArraySkis = [];
    this.availableDatesArraySkiPoles = [];
    this.availableDatesArrayBoots = [];

    this.messageSnowboardError = false;
    this.messageSkisError = false;
    this.messageSkiPolesError = false;
    this.messageBootsError = false;

    this.errorReservation = false;
  }

  getAllSnowboard(single: boolean) {
    this.snowboardService.getAllSnowboard(single).subscribe(
      data => {
        console.log(data);
        if (data['snowboard']) {
          this.snowboardList = data['snowboard'];
        }
      });
  }

  getAllSkis(single: boolean) {
    this.skisService.getAllSkis(single).subscribe(
      data => {
        console.log(data);
        if (data['skis']) {
          this.skisList = data['skis'];
        }
      });
  }

  getAllSkiPoles(single: boolean) {
    this.skiPolesService.getAllSkiPoles(single).subscribe(
      data => {
        console.log(data);
        if (data['skiPoles']) {
          this.skiPolesList = data['skiPoles'];
        }
      });
  }

  getAllBoots(single: boolean) {
    this.bootsService.getAllBoots(single).subscribe(
      data => {
        console.log(data);
        if (data['boots']) {
          this.bootsList = data['boots'];
        }
      });
  }

  showAddEquipmentDialog() {
    this.displayDialogAddEquipment = true;
  }

  addEquipmentToReservation() {
    if (this.selectedEquipmentType === 'snowboard') {
      this.initSnowboardOptionsForm();
      this.getSnowboardLeg();
      this.snowboardOptionsForm.controls['idSnowboard'].setValue(this.snowboardFormControl.value);
      this.snowboardList.forEach(element => {
        if (element.IdSnowboard === this.snowboardFormControl.value) {
          this.snowboard = element;
        }
      });
    } else if (this.selectedEquipmentType === 'skis') {
      this.initSkisOptionForm();
      this.skisOptionsForm.controls['idNarty'].setValue(this.skisFormControl.value);
      this.skisList.forEach(element => {
        if (element.IdNarty === this.skisFormControl.value) {
          this.skis = element;
        }
      });
    } else if (this.selectedEquipmentType === 'skiPoles') {
      this.skiPolesList.forEach(element => {
        if (element.IdKijki === this.skiPolesFormControl.value) {
          this.skiPoles = element;
        }
      });
    } else if (this.selectedEquipmentType === 'boots') {
      this.bootsList.forEach(element => {
        if (element.IdButy === this.bootsFormControl.value) {
          this.boots = element;
        }
      });
    }
    this.selectedEquipmentType = null;
    this.displayDialogAddEquipment = false;
  }

  initSnowboardOptionsForm() {
    this.snowboardOptionsForm = new FormGroup({
      idSnowboard: new FormControl(null),
      ustawienie: new FormControl(null, [Validators.required]),
      katL: new FormControl(45, [Validators.required, CustomValidators.validateNumeric]),
      katP: new FormControl(45, [Validators.required, CustomValidators.validateNumeric]),
    });

    this.snowboardOptionsForm.valueChanges.subscribe((data) => {
      this.formErrorsSnowboardOptions = this.formCheckService.validateForm(this.snowboardOptionsForm,
        this.formErrorsSnowboardOptions, true);
      });
  }

  initReservationForm() {
    this.reservationForm = new FormGroup({
      dataOd: new FormControl(this.dateFrom, [Validators.required]),
      dataDo: new FormControl(null, [Validators.required]),
      recaptcha: new FormControl(null, [Validators.required]),
    });
  }

  initSkisOptionForm() {
    this.skisOptionsForm = new FormGroup({
      idNarty: new FormControl(null),
      waga: new FormControl(null, [Validators.required, CustomValidators.validateNumeric]),
      rozmiarButa: new FormControl(null, [Validators.required, CustomValidators.validateSizeBootsPattern])
    });
    this.skisOptionsForm.valueChanges.subscribe((data) => {
      this.formErrorsSkisOptions = this.formCheckService.validateForm(this.skisOptionsForm, this.formErrorsSkisOptions, true);
      });
  }

  initCustomerForm() {
    this.customerForm = new FormGroup({
      imie: new FormControl(null, [Validators.required, CustomValidators.validateAlphanumeric, CustomValidators.validateLength]),
      nazwisko: new FormControl(null, [Validators.required, CustomValidators.validateAlphanumeric, CustomValidators.validateLength]),
      telefon: new FormControl(null, [Validators.required, CustomValidators.validatePhone]),
      email: new FormControl(null, [Validators.required, Validators.email]),
    });

    this.customerForm.valueChanges.subscribe((data) => {
    this.formErrorsCustomer = this.formCheckService.validateForm(this.customerForm, this.formErrorsCustomer, true);
    });
  }

  skisOptions(index: number) {
    this.reservationService.getSkisOptions(index).subscribe(
      data => {
        console.log(data);
        this.optionsSkis = data['skisOptions'][0];
        this.displaySkisOptions = true;
      }
    );
  }

  snowboardOptions(index: number){
    this.reservationService.getSnowboardOptions(index).subscribe(
      data => {
        console.log(data);
        this.optionsSnowboard = data['snowboardOptions'][0];
        this.displaySnowboardOptions = true;
      }
    );
  }

  getSnowboardLeg() {
    this.snowboardService.getSnowboardLeg().subscribe(
      snowboardLeg => {
        this.snowboardLeg = snowboardLeg['snowboardUstawienie'];
        this.snowboardOptionsForm.controls['ustawienie'].setValue(this.snowboardLeg[0].Id);
    });
  }

   deleteSnowboard() {
    this.clearAllMessages();
    this.snowboard = null;
    this.selectedEquipmentType = null;
    this.reservationData.snowboardOptions = null;
   }

   deleteSkis() {
    this.clearAllMessages();
    this.skis = null;
    this.reservationData.skisOptions = null;
   }

   deleteSkiPoles() {
    this.clearAllMessages();
    this.skiPoles = null;
    this.reservationData.skiPoles = null;
   }

   deleteBoots() {
    this.clearAllMessages();
    this.boots = null;
    this.reservationData.boots = null;
   }

   getAllReservation() {
    const tmp = {'period' : this.selectedPeriod, 'status': this.selectedStatus};
    console.log(tmp);
    this.reservationService.getAllReservation(tmp).subscribe(
      data => {
        console.log('Get all');
        console.log(data);
        if (data['reservations'] != null) {
          this.reservations = data['reservations'];
        } else {
          this.reservations = null;
        }
      }
    );
   }

   changeStatus(reservation: number, status: number) {
      const tmp = {'idReservation' : reservation, 'idStatus' : status};
      this.reservationService.changeStatusReservation(tmp).subscribe(
        data => {
          console.log(data);
          if (data['msg']) {
            this.messageService.add({severity: 'success', summary: 'Rezerwacja', detail: data['msg']});
            this.getAllReservation();
          } else if (data['error']) {
            this.messageService.add({severity: 'error', summary: 'Rezerwacja', detail: data['error']});
          }
        }
      );
   }

   cancelDialogReservation(idReservation: number, idCustomer: number) {
    this.cancelReservationId = idReservation;
    this.customerId = idCustomer;
    this.displayDialogCancel = true;
   }

   cancelReservation(idReservation: number) {
    this.displayDialogCancel = false;
    this.reservationService.deleteReservation(idReservation).subscribe(
      data => {
        console.log(data);
         if (data['msg']) {
          this.messageService.add({severity: 'success', summary: 'Rezerwacja', detail: data['msg']});
          this.getAllReservation();
          this.customerService.updateReservationAmount(this.customerId).subscribe(
            data2 => console.log(data2)
          );
        } else if (data['error']) {
          this.messageService.add({severity: 'error', summary: 'Rezerwacja', detail: data['error']});
        }
      },
      error => {
        console.log(error);
      }
    );
   }

   rent(idReservation: number) {
    this.rentEquipment.push(idReservation);
    console.log(this.rentEquipment);
    this.rentService.setIdReservationArray(this.rentEquipment);
    this.rentService.setFormView(true);
    this.router.navigateByUrl('/wypozyczenie');
   }

   /*
   rent(idReservation: number, option: boolean) {
    // dodaje do tabeli
    if (option) {
      this.rentEquipment.push(idReservation);
      console.log(this.rentEquipment);

      let idKlient = null;
      let dataOd = null;
      let dataDo = null;
      this.reservations.forEach(element => {
        if (element.Id === idReservation) {
           idKlient = element.IdKlient;
           dataOd = element.DataOd;
           dataDo = element.DataDo;
        }
      });

      this.reservations.forEach(element => {
        if (idKlient !== null && idKlient === element.IdKlient && dataOd !== null && dataOd === element.DataOd
          && dataDo !== null && dataDo === element.DataDo) {
            if (!this.possibleMultipleRent.includes(element.Id)) {
              this.possibleMultipleRent.push(element.Id);
            }
        }
      });

      console.log(this.possibleMultipleRent);

      // usuwa z tabeli
     } else {
      const indexOfElement = this.rentEquipment.indexOf(idReservation);
      this.rentEquipment.splice(indexOfElement, 1);

      if (this.rentEquipment.length === 0) {
        this.possibleMultipleRent = [];
      }
      // indexOfElement = this.possibleMultipleRent.indexOf(idReservation);
      // this.possibleMultipleRent.splice(indexOfElement, 1);

      console.log(this.rentEquipment);
     }

    this.rentService.setIdReservationArray(this.rentEquipment);
   }*/

   redirectToRenting() {
    this.router.navigateByUrl('/wypozyczenie');
   }

   reserve() {
     let errorReservation = false;
    // sprawdzenie czy byl snowboard i czy formularz snowboard wypelniony
    if (this.snowboard && this.snowboardOptionsForm.invalid) {
      this.messageService.add({severity: 'error', summary: 'Rezerwacja snowboard', detail: 'Niepoprawne dane w formularzu'});
      errorReservation = true;
    } else if (this.snowboard) {
      this.reservationData.snowboardOptions = this.snowboardOptionsForm.value;
    }
    if (this.skis && this.skisOptionsForm.invalid) {
      this.messageService.add({severity: 'error', summary: 'Rezerwacja nart', detail: 'Niepoprawne dane w formularzu'});
      errorReservation = true;
    } else if (this.skis) {
      this.reservationData.skisOptions = this.skisOptionsForm.value;
    }
    if (this.customerForm.invalid) {
      this.messageService.add({severity: 'error', summary: 'Rezerwacja', detail: 'Uzupełnij swoje dane'});
      errorReservation = true;
    } else {
      this.reservationData.customer = this.customerForm.value;
    }
    if (this.reservationForm.controls['dataOd'].value === null ) {
      this.messageService.add({severity: 'error', summary: 'Rezerwacja', detail: 'Brak daty końcowej'});
      errorReservation = true;
    } else if (this.reservationForm.controls['dataDo'].value < this.reservationForm.controls['dataOd'].value) {
      this.messageService.add({severity: 'error', summary: 'Rezerwacja', detail: 'Nieprawidłowa data końcowa'});
      errorReservation = true;
    } else {
      this.reservationData.dateFrom = this.reservationForm.controls['dataOd'].value;
      this.reservationData.dateTo = this.reservationForm.controls['dataDo'].value;
    }
    if (this.skiPoles) {
      this.reservationData.skiPoles = this.skiPoles.IdKijki;
    }
    if (this.boots) {
      this.reservationData.boots = this.boots.IdButy;
    }

    if (!errorReservation) {
      console.log(this.reservationData);

      this.reservationService.addReservation(this.reservationData).subscribe(
        data => {
          if (data['msg']) {
            this.clearAllMessages();
            this.deleteSnowboard();
            this.deleteSkis();
            this.deleteBoots();
            this.deleteSkiPoles();
            this.messageService.add({severity: 'success', summary: 'Rezerwacja', detail: data['msg']});
          } else if (data['error'] === 'Przekroczono maksymalną liczbę rezerwacji, która wynosi 3') {
            this.messageService.add({severity: 'error', summary: 'Rezerwacja', detail: data['error']});
          } else {

            this.messageService.add({severity: 'error', summary: 'Rezerwacja', detail: data['error']});

            if (this.snowboard) {
              if (data['snowboard']['error']) {
                this.occupiedDatesArraySnowboard = data['snowboard']['occupied'];
                this.messageSnowboardError = true;
                if (data['snowboard']['available']) {
                  this.availableDatesArraySnowboard = data['snowboard']['available'];
                }
              } else if (data['snowboard']['msg']) {
                this.messageSnowboardError = false;
              }
            }

            if (this.skis) {
              if (data['skis']['error']) {
                this.occupiedDatesArraySkis = data['skis']['occupied'];
                this.messageSkisError = true;
                if (data['skis']['available']) {
                  this.availableDatesArraySkis = data['skis']['available'];
                }
              } else if (data['skis']['msg']) {
                this.messageSkisError = false;
              }
            }

            if (this.skiPoles) {
              if (data['skiPoles']['error']) {
                this.occupiedDatesArraySkiPoles = data['skiPoles']['occupied'];
                this.messageSkiPolesError = true;
                if (data['skiPoles']['available']) {
                  this.availableDatesArraySkiPoles = data['skiPoles']['available'];
                }
              } else if (data['skiPoles']['msg']) {
                this.messageSkiPolesError = false;
              }
            }

            if (this.boots) {
              if (data['boots']['error']) {
                this.occupiedDatesArrayBoots = data['boots']['occupied'];
                this.messageBootsError = true;
                if (data['boots']['available']) {
                  this.availableDatesArrayBoots = data['boots']['available'];
                }
              } else if (data['boots']['msg']) {
                this.messageBootsError = false;
              }
            }
            this.errorReservation = true;
          }
          console.log(data);
        },
        error => {
          console.log(error);
        }
      );
    }

   }

}
