import { Component, OnInit, Inject } from '@angular/core';
import { SkiPolesService } from './ski-poles.service';
import { ProducerService } from '../producer/producer.service';
import { GenderService } from '../gender/gender.service';
import { FormCheckService } from '../validators/form-check.service';
import {MessageService, SelectItem} from 'primeng/api';
import {AppRoutingService} from '../app-routing/app-routing.service';
import {RentService} from '../rent/rent.service';
import {ServiceService} from '../service/service.service';
import { AccessService } from '../access/access.service';

import { SkiPoles } from '../shared/ski-poles';
import { Producer } from '../shared/producer';
import { Gender } from '../shared/gender';

import { Router } from '@angular/router';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { CustomValidators } from '../validators/customValidators';
import { SESSION_STORAGE, StorageService } from 'angular-webstorage-service';

@Component({
  selector: 'app-ski-poles',
  templateUrl: './ski-poles.component.html',
  styleUrls: ['./ski-poles.component.css']
})
export class SkiPolesComponent implements OnInit {

  skiPoles: SkiPoles[];
  producer: Producer[];
  gender: Gender[];
  adminPanelView: any;
  skiPolesForm: FormGroup;
  searchByDateForm: FormGroup;

  skiPolesHistory: any[];
  displayHistory: boolean;
  viewGroup: boolean;


  displayDialogDelete = false;
  idSkiPolesToDelete: number;
  isEditMode = false;
  toEditSkiPolesPack = {
    skiPoles: null,
    amount: null
  };
  formErrors = {
    model: null,
    dlugosc: null,
    producent: null,
    cena: null,
    plec: null,
    ilosc: null,
  };

  cols: any[];
  selectProducer: SelectItem[];
  selectGender: SelectItem[];

  isAdmin = false;
  isEmployee = false;
  isOwner = false;
  pl: any;
  minDate: any;


  constructor(private skiPolesService: SkiPolesService, private producerService: ProducerService, private genderService: GenderService,
    private formCheckService: FormCheckService, private messageService: MessageService, private appRoutingService: AppRoutingService,
    private router: Router, @Inject(SESSION_STORAGE) private sessionStorage: StorageService,
    private rentService: RentService, private serviceService: ServiceService, private accessService: AccessService) { }

  ngOnInit() {
    this.minDate = new Date();
    this.initLanguageCalendar();
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

    this.displayHistory = false;
    this.viewGroup = true;

    if (this.adminPanelView.isExtendList && this.viewGroup) {
      this.setTableColumns(true);
     } else  {
       this.setTableColumns(false);
     }
    this.isEditMode = this.skiPolesService.getIsEditMode();
    if (this.isEditMode) {
      this.skiPolesService.changeEditMode();
      this.toEditSkiPolesPack = this.skiPolesService.getEditSkiPoles();
    }

    this.getAllSkiPoles(false);
    this.getAllProducer();
    this.getAllGender();
    this.initSkiPolesForm();
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
      { field: 'Ilosc', header: 'Ilość' }
  ];
  } else {
    this.cols = [
      { field: 'NazwaProducent', header: 'Producent' },
      { field: 'Model', header: 'Model' },
      { field: 'Dlugosc', header: 'Dlugosc [cm]' },
      { field: 'Cena', header: 'Cena [zł] (dzień)' },
      { field: 'NazwaPlec', header: 'Płeć użytkownika' }
  ];
  }
}

  initSkiPolesForm(afterAdd?: boolean) {
    this.skiPolesForm = new FormGroup({
      id: new FormControl(null),
      model: new FormControl(null, [Validators.required, CustomValidators.validateAlphanumeric, CustomValidators.validateLength]),
      dlugosc: new FormControl(null, [Validators.required, CustomValidators.validateNumeric]),
      producent: new FormControl(null, Validators.required),
      cena: new FormControl(null, [Validators.required, CustomValidators.validatePricePattern]),
      plec: new FormControl(null, [Validators.required]),
      ilosc: new FormControl(1, [Validators.required, CustomValidators.validateAmountPattern])
    });

    if (this.isEditMode) {
      this.skiPolesForm.controls['id'].setValue(this.toEditSkiPolesPack.skiPoles.IdKijki);
      this.skiPolesForm.controls['model'].setValue(this.toEditSkiPolesPack.skiPoles.Model);
      this.skiPolesForm.controls['dlugosc'].setValue(this.toEditSkiPolesPack.skiPoles.Dlugosc);
      this.skiPolesForm.controls['cena'].setValue(this.toEditSkiPolesPack.skiPoles.Cena);
      this.skiPolesForm.controls['ilosc'].setValue(this.toEditSkiPolesPack.amount);
      this.skiPolesForm.controls['producent'].setValue(this.toEditSkiPolesPack.skiPoles.IdProducent);
      this.skiPolesForm.controls['plec'].setValue(this.toEditSkiPolesPack.skiPoles.IdPlec);
    } else if (afterAdd) {
      this.skiPolesForm.controls['producent'].setValue(this.producer[0].Id);
      this.skiPolesForm.controls['plec'].setValue(this.gender[0].Id);
     }

    this.skiPolesForm.valueChanges.subscribe((data) => {
      this.formErrors = this.formCheckService.validateForm(this.skiPolesForm, this.formErrors, true);
    });
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
      this.getAllSkiPoles(false);
      this.cols = [
        { field: 'NazwaProducent', header: 'Producent' },
        { field: 'Model', header: 'Model' },
        { field: 'Dlugosc', header: 'Dlugosc [cm]' },
        { field: 'Cena', header: 'Cena [zł] (dzień)' },
        { field: 'NazwaPlec', header: 'Płeć użytkownika' },
        { field: 'Ilosc', header: 'Ilość' }
    ];
    } else {
      this.getAllSkiPoles(true);
      this.cols = [
        { field: 'Id', header: 'Numer kijków' },
        { field: 'NazwaProducent', header: 'Producent' },
        { field: 'Model', header: 'Model' },
        { field: 'Dlugosc', header: 'Dlugosc [cm]' },
        { field: 'Cena', header: 'Cena [zł] (dzień)' },
        { field: 'NazwaPlec', header: 'Płeć użytkownika' }
    ];
    }
  }

  reset() {
    this.viewGroup = true;
    this.changeView();
    this.searchByDateForm.controls['dataDo'].setValue(null);
    this.searchByDateForm.controls['dataOd'].setValue(null);
  }

  getEquipmentHistory(id: number) {
    this.serviceService.getOneServiceEquipmentHistory(id).subscribe(
      data => {
        console.log(data);
        if (data['error'] === null) {
          this.skiPolesHistory = data['history'];
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
    this.skiPolesService.getByDate(this.searchByDateForm.value).subscribe(
      data => {
        console.log(data);
        this.skiPoles = data['wolne'];
      }
    );
    this.viewGroup = false;
      this.cols = [
        { field: 'Id', header: 'Numer deski' },
        { field: 'NazwaProducent', header: 'Producent' },
        { field: 'Model', header: 'Model' },
        { field: 'Dlugosc', header: 'Dlugosc [cm]' },
        { field: 'Cena', header: 'Cena (dzień)' },
        { field: 'NazwaPlec', header: 'Płeć użytkownika' }
    ];
  }
}

  rent(index: number) {
    this.rentService.setIdEquipentToRent(index, 'skiPoles');
    this.rentService.setFormView(true);
    this.router.navigateByUrl('/wypozyczenie');
  }

  reserveSkiPoles(indexSkiPoles: number) {
    this.sessionStorage.set('skiPoles', indexSkiPoles);
  }

  getAllSkiPoles(single: boolean): void {
    this.skiPolesService.getAllSkiPoles(single).subscribe(
      data => {
        if (data['skiPoles']) {
          this.skiPoles = data['skiPoles'];
        } else if (data['error']) {
          this.messageService.add({severity: 'error', summary: 'Kijki', detail: data['error']});
        }
      },
      error => this.messageService.add({severity: 'error', summary: 'Kijki', detail: 'Nie udało się pobrać danych'})
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
          // this.skiPolesForm.controls['producent'].setValue(producer[this.toEditSkiPolesPack.skiPoles.IdProducent - 1].Id);
         } else {
          this.skiPolesForm.controls['producent'].setValue(producer[0].Id);
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
           this.skiPolesForm.controls['plec'].setValue(gender[this.toEditSkiPolesPack.skiPoles.IdPlec - 1].Id);
        } else {
          this.skiPolesForm.controls['plec'].setValue(gender[0].Id);
        }
      }
    );
  }

  showDialogDelete(indexSkiPoles) {
    this.displayDialogDelete = true;
    this.idSkiPolesToDelete = indexSkiPoles;
  }


  addSkiPoles() {
    console.log(this.skiPolesForm.value);
    this.skiPolesService.addSkiPoles(this.skiPolesForm.value, this.isEditMode).subscribe(
      data => {
        console.log(data);
        if (data['msg']) {
          this.messageService.add({severity: 'success', summary: 'Kijki', detail: data['msg']});
          this.isEditMode = false;
          this.initSkiPolesForm(true);
        } else  if (data['error'])  {
          this.messageService.add({severity: 'error', summary: 'Kijki', detail: data['error']});
        }
      },
      error => {
        this.messageService.add({severity: 'error', summary: 'Kijki', detail: error});
      }
    );
  }

  editSkiPoles(index: number, amount: number) {
    this.skiPolesService.getOneSkiPoles(index).subscribe(
      data => {
        // this.toEditSnowboard = data[0];
        this.skiPolesService.setEditSkiPoles(data[0], amount);
        // console.log(this.toEditSnowboard);
        // console.log(this.toEditSnowboard.Model);
        this.router.navigateByUrl('/zarzadzanie/(adminContent:kijki-formularz)');
        // console.log(this.toEditSnowboard);

      },
      error => this.messageService.add({severity: 'error', summary: 'Kijki', detail: 'Nie udało się pobrać sprzętu'})
    );
  }

  deleteSkiPoles(index: number) {
    this.displayDialogDelete = false;
    this.skiPolesService.deleteSkiPoles(index).subscribe(
      data => {
        console.log(data);
        if (data['msg']) {
          this.messageService.add({severity: 'success', summary: 'Kijki', detail: data['msg']});
          this.getAllSkiPoles(false);
        } else  if (data['error'])  {
          this.messageService.add({severity: 'error', summary: 'Kijki', detail: data['error']});
        }
      },
      error => console.log(error)
    );
  }

}
