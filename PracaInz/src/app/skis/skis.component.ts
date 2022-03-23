import { Component, OnInit, Inject } from '@angular/core';
import { SkisService } from './skis.service';
import { DestinyService } from '../destiny/destiny.service';
import { ProducerService } from '../producer/producer.service';
import { GenderService } from '../gender/gender.service';
import { FormCheckService } from '../validators/form-check.service';
import {MessageService, SelectItem} from 'primeng/api';
import {AppRoutingService} from '../app-routing/app-routing.service';
import {RentService} from '../rent/rent.service';
import { ServiceService } from '../service/service.service';
import { AccessService } from '../access/access.service';

import { Skis } from '../shared/skis';
import { Type } from '../shared/destiny';
import { Producer } from '../shared/producer';
import { Gender } from '../shared/gender';

import { Router } from '@angular/router';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { CustomValidators } from '../validators/customValidators';
import { SESSION_STORAGE, StorageService } from 'angular-webstorage-service';



@Component({
  selector: 'app-skis',
  templateUrl: './skis.component.html',
  styleUrls: ['./skis.component.css']
})
export class SkisComponent implements OnInit {

  skis: Skis[];
  type: Type[];
  producer: Producer[];
  gender: Gender[];

  skisForm: FormGroup;
  searchByDateForm: FormGroup;

  skisHistory: any[];
  displayHistory: boolean;
  viewGroup: boolean;

  adminPanelView: any;
  isEditMode: boolean;
  toEditSkisPack: any;
  displayDialogDelete = false;
  idSkisToDelete: number;
  formErrors = {
    model: null,
    przeznaczenie: null,
    dlugosc: null,
    producent: null,
    cena: null,
    plec: null,
    ilosc: null,
  };

  selectProducer: SelectItem[];
  selectGender: SelectItem[];
  selectType: SelectItem[];
  cols: any[];
  isAdmin = false;
  isEmployee = false;
  isOwner = false;
  pl: any;
  minDate: any;


  constructor(private skisService: SkisService, private typeService: DestinyService, private producerService: ProducerService,
    private genderService: GenderService, private messageService: MessageService, private appRoutingService: AppRoutingService,
    private formCheckService: FormCheckService, private router: Router, @Inject(SESSION_STORAGE) private sessionStorage: StorageService,
    private rentService: RentService, private serviceService: ServiceService, private accessService: AccessService) { }

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
    if (!this.isOwner && !this.isAdmin && !this.isEmployee && this.adminPanelView.isExtendList ) {
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

    if (this.adminPanelView.isExtendList && this.viewGroup) {
      this.setTableColumns(true);
     } else  {
       this.setTableColumns(false);
     }


    this.isEditMode = this.skisService.getIsEditMode();
    if (this.isEditMode) {
      this.skisService.changeEditMode();
      this.toEditSkisPack = this.skisService.getEditSkis();
    }
    this.getAllSkis(false);
    this.getAllType();
    this.getAllProducer();
    this.getAllGender();
    this.initSkisForm();
    this.initSearchByDateForm();
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
      this.getAllSkis(false);
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
      this.getAllSkis(true);
      this.cols = [
        { field: 'Id', header: 'Numer nart' },
        { field: 'NazwaProducent', header: 'Producent' },
        { field: 'Model', header: 'Model' },
        { field: 'Dlugosc', header: 'Dlugosc [cm]' },
        { field: 'Cena', header: 'Cena [zł] (dzień)' },
        { field: 'NazwaPlec', header: 'Płeć użytkownika' },
        { field: 'NazwaPrzeznaczenie', header: 'Przeznaczenie jazdy' }
    ];
    }
  }

  reset() {
    this.viewGroup = true;
    this.changeView();
    this.searchByDateForm.controls['dataDo'].setValue(null);
    this.searchByDateForm.controls['dataOd'].setValue(null);
  }


  initSkisForm(afterAdd?: boolean) {
    this.skisForm = new FormGroup({
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
      console.log(this.toEditSkisPack);
      this.skisForm.controls['id'].setValue(this.toEditSkisPack.skis.IdNarty);
      this.skisForm.controls['model'].setValue(this.toEditSkisPack.skis.Model);
      this.skisForm.controls['dlugosc'].setValue(this.toEditSkisPack.skis.Dlugosc);
      this.skisForm.controls['cena'].setValue(this.toEditSkisPack.skis.Cena);
      this.skisForm.controls['ilosc'].setValue(this.toEditSkisPack.amount);
      this.skisForm.controls['przeznaczenie'].setValue(this.toEditSkisPack.skis.IdPrzeznaczenie);
      this.skisForm.controls['producent'].setValue(this.toEditSkisPack.skis.IdProducent);
      this.skisForm.controls['plec'].setValue(this.toEditSkisPack.skis.IdPlec);
    } else if (afterAdd) {
      this.skisForm.controls['przeznaczenie'].setValue(this.type[0].Id);
      this.skisForm.controls['producent'].setValue(this.producer[0].Id);
      this.skisForm.controls['plec'].setValue(this.gender[0].Id);
    }

    this.skisForm.valueChanges.subscribe((data) => {
      this.formErrors = this.formCheckService.validateForm(this.skisForm, this.formErrors, true);
    });
  }

  getEquipmentHistory(id: number) {
    this.serviceService.getOneServiceEquipmentHistory(id).subscribe(
      data => {
        console.log(data);
        if (data['error'] === null) {
          this.skisHistory = data['history'];
          this.displayHistory = !this.displayHistory;
        } else {
          this.messageService.add({severity: 'info', summary: 'Historia serwisowa', detail: data['error']});
        }
      }
    );
  }


  initSearchByDateForm() {
    this.searchByDateForm = new FormGroup({
      dataOd: new FormControl(null),
      dataDo: new FormControl(null),
    });
  }

  searchByDate() {
    if (this.searchByDateForm.controls['dataDo'].value < this.searchByDateForm.controls['dataOd'].value) {
      this.messageService.add({severity: 'error', summary: 'Rezerwacja', detail: 'Nieprawidłowa data końcowa'});
    } else {
    this.skisService.getByDate(this.searchByDateForm.value).subscribe(
      data => {
        console.log(data);
        this.skis = data['wolne'];
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

  rent(index: number) {
    console.log(index);
    this.rentService.setIdEquipentToRent(index, 'skis');
    this.rentService.setFormView(true);
    this.router.navigateByUrl('/wypozyczenie');
  }

  reserveSkis(indexSkis: number) {
    this.sessionStorage.set('skis', indexSkis);
  }

  addSkis() {
    this.skisService.addSkis(this.skisForm.value, this.isEditMode).subscribe(
      data => {
        console.log(data);
        if (data['msg']) {
          this.messageService.add({severity: 'success', summary: 'Narty', detail: data['msg']});
          this.isEditMode = false;
          this.initSkisForm(true);
        } else  if (data['error'])  {
          this.messageService.add({severity: 'error', summary: 'Narty', detail: data['error']});
        }
      },
      error => {
        this.messageService.add({severity: 'error', summary: 'Narty', detail: error});
      }
    );
  }

  editSkis(index: number, amount: number) {
    this.skisService.getOneSkis(index).subscribe(
      data => {
        // this.toEditSnowboard = data[0];
        this.skisService.setEditSkis(data[0], amount);
        // console.log(this.toEditSnowboard);
        // console.log(this.toEditSnowboard.Model);
        this.router.navigateByUrl('/zarzadzanie/(adminContent:narty-formularz)');
        // console.log(this.toEditSnowboard);

      },
      error => this.messageService.add({severity: 'error', summary: 'Narty', detail: 'Nie udało się pobrać sprzętu'})
    );
  }

  deleteSkis(index: number) {
    this.displayDialogDelete = false;
    this.skisService.deleteSkis(index).subscribe(
      data => {
        console.log(data);
        if (data['msg']) {
          this.messageService.add({severity: 'success', summary: 'Narty', detail: data['msg']});
          this.getAllSkis(false);
        } else  if (data['error'])  {
          this.messageService.add({severity: 'error', summary: 'Narty', detail: data['error']});
        }
      },
      error => console.log(error)
    );
  }

  showDialogDelete(indexSkis) {
    this.displayDialogDelete = true;
    this.idSkisToDelete = indexSkis;
  }

  getAllSkis(single: boolean): void {
    this.skisService.getAllSkis(single).subscribe(
      data => {
        if (data['skis']) {
          this.skis = data['skis'];
        } else if (data['error']) {
          this.messageService.add({severity: 'error', summary: 'Narty', detail: data['error']});
        }
      },
      error => this.messageService.add({severity: 'error', summary: 'Narty', detail: 'Nie udało się pobrać danych'})
    );
  }

  getAllType(): void {
    this.typeService.getAllDestiny().subscribe(
      type => {
        this.type = type;
        this.type.forEach(element => {
          this.selectType.push({label: element.NazwaPrzeznaczenie, value: element.NazwaPrzeznaczenie});
        });
        console.log(this.selectType);
        if (this.isEditMode) {
          // this.skisForm.controls['przeznaczenie'].setValue(type[this.toEditSkisPack.skis.IdPrzeznaczenie - 1].Id);
        } else {
          this.skisForm.controls['przeznaczenie'].setValue(type[0].Id);
        }
      }
    );
  }
  getAllProducer(): void {
    this.producerService.getAllProducer().subscribe(
      producer => {
        this.producer = producer;
        this.producer.forEach(element => {
          this.selectProducer.push({label: element.NazwaProducent, value: element.NazwaProducent});
        });
        if (this.isEditMode) {
          // this.skisForm.controls['producent'].setValue(producer[this.toEditSkisPack.skis.IdProducent - 1].Id);
        } else {
          this.skisForm.controls['producent'].setValue(producer[0].Id);
        }
      }
    );
  }
  getAllGender(): void {
    this.genderService.getAllGender().subscribe(
      gender => {
        this.gender = gender;
        this.gender.forEach(element => {
          this.selectGender.push({label: element.NazwaPlec, value: element.NazwaPlec});
        });
        if (this.isEditMode) {
         // this.skisForm.controls['plec'].setValue(gender[this.toEditSkisPack.skis.IdPlec - 1].Id);
        } else {
          this.skisForm.controls['plec'].setValue(gender[0].Id);
        }
      }
    );
  }
}
