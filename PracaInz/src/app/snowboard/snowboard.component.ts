import { Component, OnInit, Inject } from '@angular/core';
import { formatDate } from '@angular/common';

import { SnowboardService } from './snowboard.service';
import { AccessService } from '../access/access.service';
import { DestinyService } from '../destiny/destiny.service';
import { ProducerService } from '../producer/producer.service';
import { GenderService } from '../gender/gender.service';
import { RentService } from '../rent/rent.service';
import { ServiceService } from '../service/service.service';

import { Snowboard } from '../shared/snowboard';
import { Type } from '../shared/destiny';
import { Producer } from '../shared/producer';
import { Gender } from '../shared/gender';

import { Router } from '@angular/router';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { CustomValidators } from '../validators/customValidators';
import { FormCheckService } from '../validators/form-check.service';

import { MessageService, SelectItem } from 'primeng/api';
import { AppRoutingService } from '../app-routing/app-routing.service';
import { SESSION_STORAGE, StorageService } from 'angular-webstorage-service';



@Component({
  selector: 'app-snowboard',
  templateUrl: './snowboard.component.html',
  styleUrls: ['./snowboard.component.css']
})
export class SnowboardComponent implements OnInit {
  snowboard: Snowboard[];
  currentUrl: string;
  // isExtendList: boolean;
  // isForm: boolean;
  adminPanelView: any;

  snowboardForm: FormGroup;
  searchByDateForm: FormGroup;

  snowboardHistory: any[];
  displayHistory: boolean;
  viewGroup: boolean;

  type: Type[];
  producer: Producer[];
  gender: Gender[];
  toEditSnowboardPack: any;
  isEditMode: boolean;
  display = false;
  idSnowboardToDelete: number;


  formErrors = {
    model: null,
    przeznaczenie: null,
    dlugosc: null,
    producent: null,
    cena: null,
    plec: null,
    ilosc: null,
  };

  test: any;
  characters = /[!@#]/;

  cols: any[];
  selectProducer: SelectItem[];
  selectGender: SelectItem[];
  selectType: SelectItem[];

  isAdmin = false;
  isEmployee = false;
  isOwner = false;
  pl: any;
  minDate: any;

  constructor(private snowboardService: SnowboardService, private router: Router, private accessService: AccessService,
      private destinyService: DestinyService, private producerService: ProducerService, private genderService: GenderService,
      private formCheckService: FormCheckService, private messageService: MessageService,
      private appRoutingService: AppRoutingService, @Inject(SESSION_STORAGE) private sessionStorage: StorageService,
      private rentService: RentService, private serviceService: ServiceService) { }

  ngOnInit() {
    this.initLanguageCalendar();
    this.minDate = new Date();
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

    if (!this.isOwner && !this.isAdmin && !this.isEmployee && this.adminPanelView.isExtendList) {
      this.router.navigateByUrl('/error');
    } else {

    this.selectProducer = [
      { label: 'brak filtru', value: null }
  ];
    this.selectGender = [
      { label: 'brak filtru', value: null }
  ];
  this.selectType = [
    { label: 'brak filtru', value: null }
];




    this.displayHistory = false;
    this.viewGroup = true;
    this.isEditMode = this.snowboardService.getIsEditMode();
    if (this.isEditMode) {
      this.snowboardService.changeEditMode();
      this.toEditSnowboardPack = this.snowboardService.getEditSnowboard();
      console.log(this.toEditSnowboardPack);
    }
    // this.currentUrl = this.router.url;

    console.log(this.adminPanelView);
    if (this.adminPanelView.isExtendList && this.viewGroup) {
     this.setTableColumns(true);
    } else  {
      this.setTableColumns(false);
    }

    /*
    // ustalenie na jakim widoku sie znajdujemy - ulatwienie wyswietlenie odpowiedniej tresci
    if (this.accessService.getIsAdmin() && /adminContent/.test(this.currentUrl) && /formularz/.test(this.currentUrl)) {
      this.isExtendList = false;
      this.isForm = true;
    } else if (this.accessService.getIsAdmin() === false || !(/adminContent/.test(this.currentUrl))) {
      this.isExtendList = false;
      this.isForm = false;
    } else {
      this.isExtendList = true;
      this.isForm = false;
    }
    */

    this.getAllSnowboard(false);
    this.getAllDestiny();
    this.getAllGender();
    this.getAllProducer();

    this.initSnowboardForm();
    this.initSearchByDateForm();
    this.test = this.characters.test('sad@');
  }
  }

  setTableColumns(extended: boolean) {
    if (extended) {
      this.cols = [
        { field: 'NazwaProducent', header: 'Producent' },
        { field: 'Model', header: 'Model' },
        { field: 'Dlugosc', header: 'Dlugosc [cm]' },
        { field: 'Cena', header: 'Cena [zł] (dzień)' },
        { field: 'NazwaPlec', header: 'Płeć użytkownika' },
        { field: 'NazwaPrzeznaczenie', header: 'Przeznaczenie jazdy' },
        { field: 'Ilosc', header: 'Ilość' }
    ];
    } else {
      this.cols = [
        { field: 'NazwaProducent', header: 'Producent' },
        { field: 'Model', header: 'Model' },
        { field: 'Dlugosc', header: 'Dlugosc [cm]' },
        { field: 'Cena', header: 'Cena [zł] (dzień)' },
        { field: 'NazwaPlec', header: 'Płeć użytkownika' },
        { field: 'NazwaPrzeznaczenie', header: 'Przeznaczenie jazdy' }
    ];
    }
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
      dateFormat: 'mm/dd/yy'
    };
  }

  changeView() {
    if (this.viewGroup) {
      this.getAllSnowboard(false);
      this.cols = [
        { field: 'NazwaProducent', header: 'Producent' },
        { field: 'Model', header: 'Model' },
        { field: 'Dlugosc', header: 'Dlugosc [cm]' },
        { field: 'Cena', header: 'Cena [zł] (dzień)' },
        { field: 'NazwaPlec', header: 'Płeć użytkownika' },
        { field: 'NazwaPrzeznaczenie', header: 'Przeznaczenie jazdy' },
        { field: 'Ilosc', header: 'Ilość' }
    ];
    } else {
      this.getAllSnowboard(true);
      this.cols = [
        { field: 'Id', header: 'Numer deski' },
        { field: 'NazwaProducent', header: 'Producent' },
        { field: 'Model', header: 'Model' },
        { field: 'Dlugosc', header: 'Dlugosc [cm]' },
        { field: 'Cena', header: 'Cena [zł] (dzień)' },
        { field: 'NazwaPlec', header: 'Płeć użytkownika' },
        { field: 'NazwaPrzeznaczenie', header: 'Przeznaczenie jazdy' }
    ];
    }
  }

  initSnowboardForm(afterAdd?: boolean) {
    this.snowboardForm = new FormGroup({
      id: new FormControl(null),
      model: new FormControl(null, [Validators.required, CustomValidators.validateAlphanumeric, CustomValidators.validateLength]),
      przeznaczenie: new FormControl(null, [Validators.required]),
      dlugosc: new FormControl(null, [Validators.required, CustomValidators.validateNumeric]),
      producent: new FormControl(null, Validators.required),
      cena: new FormControl(null, [Validators.required, CustomValidators.validatePricePattern]),
      plec: new FormControl(null, [Validators.required]),
      ilosc: new FormControl(1, [Validators.required, CustomValidators.validateAmountPattern])
    });

    if (this.isEditMode) {
      console.log('edit');
      console.log(this.toEditSnowboardPack);
      this.snowboardForm.controls['id'].setValue(this.toEditSnowboardPack.snowboard.IdSnowboard);
      this.snowboardForm.controls['model'].setValue(this.toEditSnowboardPack.snowboard.Model);
      this.snowboardForm.controls['przeznaczenie'].setValue(this.toEditSnowboardPack.snowboard.IdPrzeznaczenie);
      this.snowboardForm.controls['dlugosc'].setValue(this.toEditSnowboardPack.snowboard.Dlugosc);
      this.snowboardForm.controls['producent'].setValue(this.toEditSnowboardPack.snowboard.IdProducent);
      this.snowboardForm.controls['cena'].setValue(this.toEditSnowboardPack.snowboard.Cena);
      this.snowboardForm.controls['plec'].setValue(this.toEditSnowboardPack.snowboard.IdPlec);
      this.snowboardForm.controls['ilosc'].setValue(this.toEditSnowboardPack.amount);
    } else if (afterAdd) {
      this.snowboardForm.controls['przeznaczenie'].setValue(this.type[0].Id);
      this.snowboardForm.controls['producent'].setValue(this.producer[0].Id);
      this.snowboardForm.controls['plec'].setValue(this.gender[0].Id);
    }

    this.snowboardForm.valueChanges.subscribe((data) => {
      this.formErrors = this.formCheckService.validateForm(this.snowboardForm, this.formErrors, true);
    });
  }

  initSearchByDateForm() {
    this.searchByDateForm = new FormGroup({
      dataOd: new FormControl(null),
      dataDo: new FormControl(null),
    });
  }

  reset() {
    this.viewGroup = true;
    this.changeView();
    this.searchByDateForm.controls['dataDo'].setValue(null);
    this.searchByDateForm.controls['dataOd'].setValue(null);
  }

  searchByDate() {

    if (this.searchByDateForm.controls['dataDo'].value < this.searchByDateForm.controls['dataOd'].value) {
      this.messageService.add({severity: 'error', summary: 'Rezerwacja', detail: 'Nieprawidłowa data końcowa'});
    } else {
      this.snowboardService.getByDate(this.searchByDateForm.value).subscribe(
        data => {
          console.log(data);
          this.snowboard = data['wolne'];
        }
      );
      this.viewGroup = false;
      this.cols = [
        { field: 'Id', header: 'Numer deski' },
        { field: 'NazwaProducent', header: 'Producent' },
        { field: 'Model', header: 'Model' },
        { field: 'Dlugosc', header: 'Dlugosc [cm]' },
        { field: 'Cena', header: 'Cena (dzień)' },
        { field: 'NazwaPlec', header: 'Płeć użytkownika' },
        { field: 'NazwaPrzeznaczenie', header: 'Przeznaczenie jazdy' }
    ];
    }
  }

  getEquipmentHistory(id: number) {
    this.serviceService.getOneServiceEquipmentHistory(id).subscribe(
      data => {
        console.log(data);
        if (data['error'] === null) {
          this.snowboardHistory = data['history'];
          this.displayHistory = !this.displayHistory;
        } else {
          this.messageService.add({severity: 'info', summary: 'Historia serwisowa', detail: data['error']});
        }
      }
    );
  }

  rent(index: number) {
    console.log(index);
    this.rentService.setIdEquipentToRent(index, 'snowboard');
    this.rentService.setFormView(true);
    this.router.navigateByUrl('/wypozyczenie');
  }

  reserveSnowboard(indexSnowboard: number) {
    this.sessionStorage.set('snowboard', indexSnowboard);
  }

  showDialog(indexSnowboard) {
    this.display = true;
    this.idSnowboardToDelete = indexSnowboard;
}

  getAllSnowboard(single: boolean): void {



    this.snowboardService.getAllSnowboard(single).subscribe(
      data => {
        console.log(data);
        if (data['snowboard']) {
          this.snowboard = data['snowboard'];
          console.log(this.snowboard);
        } else if (data['error']) {
          this.messageService.add({severity: 'error', summary: 'Snowboard', detail: data['error']});
        }
      },
      error => {
        console.log(error);
        this.messageService.add({severity: 'error', summary: 'Snowboard', detail: 'Wymagana autoryzacja'});
      });
  }

  getAllDestiny(): void {
    this.destinyService.getAllDestiny().subscribe( type => {
      this.type = type;
      this.type.forEach(element => {
        this.selectType.push({label: element.NazwaPrzeznaczenie, value: element.NazwaPrzeznaczenie});
      });
      if (!this.isEditMode) {
        this.snowboardForm.controls['przeznaczenie'].setValue(this.type[0].Id);
      }
    });
  }

  getAllProducer(): void {
    this.producerService.getAllProducer().subscribe(producer => {
      this.producer = producer;
      this.producer.forEach(element => {
        this.selectProducer.push({label: element.NazwaProducent, value: element.NazwaProducent});
      });
      if (!this.isEditMode) {
        this.snowboardForm.controls['producent'].setValue(this.producer[0].Id);
      }
    });
  }

  getAllGender(): void {
    this.genderService.getAllGender().subscribe(gender => {
      this.gender = gender;
      this.gender.forEach(element => {
        this.selectGender.push({label: element.NazwaPlec, value: element.NazwaPlec});
      });
      if (!this.isEditMode) {
        this.snowboardForm.controls['plec'].setValue(this.gender[0].Id);
      }
    });
  }

  addSnowboard() {
    console.log(this.snowboardForm);
    this.snowboardService.addSnowboard(this.snowboardForm.value, this.isEditMode).subscribe(
      data => {
        console.log(data);
        if (data['msg']) {
          this.messageService.add({severity: 'success', summary: 'Snowboard', detail: data['msg']});
          this.isEditMode = false;
          this.initSnowboardForm(true);
        } else  if (data['error'])  {
          this.messageService.add({severity: 'error', summary: 'Snowboard', detail: data['error']});
        }
      },
      error => {
        this.messageService.add({severity: 'error', summary: 'Snowboard', detail: 'Nie udało się dodać sprzętu'});
      }
    );
  }

  editSnowboard(index: number, amount: number) {
    this.snowboardService.getOneSnowboard(index).subscribe(
      data => {
        // this.toEditSnowboard = data[0];
        this.snowboardService.setEditSnowboard(data[0], amount);
        // console.log(this.toEditSnowboard);
        // console.log(this.toEditSnowboard.Model);
        this.router.navigateByUrl('/zarzadzanie/(adminContent:snowboard-formularz)');
        // console.log(this.toEditSnowboard);

      },
      error => this.messageService.add({severity: 'error', summary: 'Snowboard', detail: 'Nie udało się usunąć sprzętu'})
    );
  }

  deleteSnowboard(index: number) {
    this.display = false;
    this.snowboardService.deleteSnowboard(index).subscribe(
      data => {
        console.log(data);
        if (data['msg']) {
          this.messageService.add({severity: 'success', summary: 'Snowboard', detail: data['msg']});
          this.getAllSnowboard(false);
        } else  if (data['error'])  {
          this.messageService.add({severity: 'error', summary: 'Snowboard', detail: data['error']});
        }
      },
      error => console.log(error)
    );
  }
}
