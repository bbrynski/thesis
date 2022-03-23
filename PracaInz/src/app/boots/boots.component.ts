import { Component, OnInit, Inject } from '@angular/core';
import { BootsService } from './boots.service';
import { ProducerService } from '../producer/producer.service';
import { GenderService } from '../gender/gender.service';
import { FormCheckService } from '../validators/form-check.service';
import { MessageService, SelectItem } from 'primeng/api';
import { AppRoutingService } from '../app-routing/app-routing.service';
import { BootsCategoryService } from '../boots-category/boots-category.service';
import { RentService} from '../rent/rent.service';
import { ServiceService} from '../service/service.service';
import { AccessService } from '../access/access.service';

import { Boots } from '../shared/boots';
import { Producer } from '../shared/producer';
import { Gender } from '../shared/gender';
import { BootsCategory } from '../shared/boots-category';

import { Router } from '@angular/router';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { CustomValidators } from '../validators/customValidators';
import { SESSION_STORAGE, StorageService } from 'angular-webstorage-service';

@Component({
  selector: 'app-boots',
  templateUrl: './boots.component.html',
  styleUrls: ['./boots.component.css']
})
export class BootsComponent implements OnInit {

  boots: Boots[];
  producer: Producer[];
  gender: Gender[];
  bootsCategory: BootsCategory[];

  bootsForm: FormGroup;
  searchByDateForm: FormGroup;

  bootsHistory: any[];
  displayHistory: boolean;
  viewGroup: boolean;

  adminPanelView: any;
  displayDialogDelete = false;
  idBootsToDelete: number;
  isEditMode: boolean;
  toEditBootsPack: any;
  formErrors = {
    model: null,
    rozmiar: null,
    kategoria: null,
    producent: null,
    cena: null,
    plec: null,
    ilosc: null,
  };

  cols: any[];
  selectProducer: SelectItem[];
  selectGender: SelectItem[];
  selectCategory: SelectItem[];

  isAdmin = false;
  isEmployee = false;
  isOwner = false;
  pl: any;
  minDate: any;

  constructor(private bootsService: BootsService, private producerService: ProducerService,
    private genderService: GenderService, private formCheckService: FormCheckService, private messageService: MessageService,
    private appRoutingService: AppRoutingService, private router: Router, private bootsCategoryService: BootsCategoryService,
    @Inject(SESSION_STORAGE) private sessionStorage: StorageService, private rentService: RentService,
    private serviceService: ServiceService, private accessService: AccessService) { }

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
    this.selectCategory = [
      { label: 'brak filtru', value: null }
    ];

    this.viewGroup = true;
    this.displayHistory = false;
    if (this.adminPanelView.isExtendList && this.viewGroup) {
      this.setTableColumns(true);
     } else  {
       this.setTableColumns(false);
     }
    this.isEditMode = this.bootsService.getIsEditMode();
    if (this.isEditMode) {
      this.bootsService.changeEditMode();
      this.toEditBootsPack = this.bootsService.getEditBoots();
    }

    this.getAllBoots(false);
    this.getAllBootsCategory();
    this.getAllProducer();
    this.getAllGender();
    this.initBootsForm();
    this.initSearchByDateForm();
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

setTableColumns(extended: boolean) {
  if (extended) {
    this.cols = [
      { field: 'NazwaProducent', header: 'Producent' },
      { field: 'Model', header: 'Model' },
      { field: 'Cena', header: 'Cena [zł] (dzień)' },
      { field: 'Rozmiar', header: 'Rozmiar' },
      { field: 'NazwaKategoria', header: 'Kategoria' },
      { field: 'NazwaPlec', header: 'Płeć użytkownika' },
      { field: 'Ilosc', header: 'Ilość' }
  ];
  } else {
    this.cols = [
      { field: 'NazwaProducent', header: 'Producent' },
      { field: 'Model', header: 'Model' },
      { field: 'Cena', header: 'Cena [zł] (dzień)' },
      { field: 'Rozmiar', header: 'Rozmiar' },
      { field: 'NazwaKategoria', header: 'Kategoria' },
      { field: 'NazwaPlec', header: 'Płeć użytkownika' }
  ];
  }
}

  initBootsForm(afterAdd?: boolean) {
    this.bootsForm = new FormGroup({
      id: new FormControl(null),
      model: new FormControl(null, [Validators.required, CustomValidators.validateAlphanumeric, CustomValidators.validateLength]),
      rozmiar: new FormControl(null, [Validators.required]),
      kategoria: new FormControl(null, Validators.required),
      producent: new FormControl(null, Validators.required),
      cena: new FormControl(null, [Validators.required, CustomValidators.validatePricePattern]),
      plec: new FormControl(null, [Validators.required]),
      ilosc: new FormControl(1, [Validators.required, CustomValidators.validateAmountPattern])
    });

    if (this.isEditMode) {
      this.bootsForm.controls['id'].setValue(this.toEditBootsPack.boots.IdButy);
      this.bootsForm.controls['model'].setValue(this.toEditBootsPack.boots.Model);
      this.bootsForm.controls['rozmiar'].setValue(this.toEditBootsPack.boots.Rozmiar);
      this.bootsForm.controls['cena'].setValue(this.toEditBootsPack.boots.Cena);
      this.bootsForm.controls['ilosc'].setValue(this.toEditBootsPack.amount);
      this.bootsForm.controls['kategoria'].setValue(this.toEditBootsPack.boots.IdKategoria);
      this.bootsForm.controls['producent'].setValue(this.toEditBootsPack.boots.IdProducent);
      this.bootsForm.controls['plec'].setValue(this.toEditBootsPack.boots.IdPlec);

    } else if (afterAdd) {
      this.bootsForm.controls['kategoria'].setValue(this.bootsCategory[0].Id);
      this.bootsForm.controls['producent'].setValue(this.producer[0].Id);
      this.bootsForm.controls['plec'].setValue(this.gender[0].Id);
    }

    this.bootsForm.valueChanges.subscribe((data) => {
      this.formErrors = this.formCheckService.validateForm(this.bootsForm, this.formErrors, true);
    });
  }

  changeView() {
    if (this.viewGroup) {
      this.getAllBoots(false);
      this.cols = [
        { field: 'NazwaProducent', header: 'Producent' },
        { field: 'Model', header: 'Model' },
        { field: 'Cena', header: 'Cena [zł] (dzień)' },
        { field: 'Rozmiar', header: 'Rozmiar' },
        { field: 'NazwaKategoria', header: 'Kategoria' },
        { field: 'NazwaPlec', header: 'Płeć użytkownika' },
        { field: 'Ilosc', header: 'Ilość' }
    ];
    } else {
      this.getAllBoots(true);
      this.cols = [
        { field: 'NazwaProducent', header: 'Producent' },
        { field: 'Model', header: 'Model' },
        { field: 'Cena', header: 'Cena [zł] (dzień)' },
        { field: 'Rozmiar', header: 'Rozmiar' },
        { field: 'NazwaKategoria', header: 'Kategoria' },
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
          this.bootsHistory = data['history'];
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
    this.bootsService.getByDate(this.searchByDateForm.value).subscribe(
      data => {
        console.log(data);
        this.boots = data['wolne'];
      }
    );
    this.viewGroup = false;
    this.cols = [
      { field: 'Id', header: 'Numer id' },
      { field: 'NazwaProducent', header: 'Producent' },
      { field: 'Model', header: 'Model' },
      { field: 'Cena', header: 'Cena (dzień)' },
      { field: 'Rozmiar', header: 'Rozmiar' },
      { field: 'NazwaKategoria', header: 'Kategoria' },
      { field: 'NazwaPlec', header: 'Płeć użytkownika' }
  ];
  }
}

  rent(index: number) {
    this.rentService.setIdEquipentToRent(index, 'boots');
    this.rentService.setFormView(true);
    this.router.navigateByUrl('/wypozyczenie');
  }

  reserveBoots(indexBoots: number) {
    this.sessionStorage.set('boots', indexBoots);
  }

  getAllBoots(single: boolean) {
    this.bootsService.getAllBoots(single).subscribe(
      data => {
        if (data['boots']) {
          this.boots = data['boots'];
        } else if (data['error']) {
          this.messageService.add({severity: 'error', summary: 'Buty', detail: data['error']});
        }
      },
      error => this.messageService.add({severity: 'error', summary: 'Buty', detail: 'Nie udało się pobrać danych'})
    );
  }

  getAllBootsCategory(): void {
    this.bootsCategoryService.getAllBootsCategory().subscribe(
      data => {
        this.bootsCategory = data;
        this.bootsCategory.forEach(element => {
          this.selectCategory.push({label: element.NazwaKategoria, value: element.NazwaKategoria});
        });
        if (this.isEditMode) {
         // this.bootsForm.controls['kategoria'].setValue(data[this.toEditBootsPack.boots.IdKategoria].Id);
        } else {
          this.bootsForm.controls['kategoria'].setValue(data[0].Id);
        }
      },
      error => console.log(error)
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
          console.log(this.toEditBootsPack.boots);
          // this.bootsForm.controls['producent'].setValue(producer[this.toEditBootsPack.boots.IdProducent]);
        } else {
          this.bootsForm.controls['producent'].setValue(producer[0].Id);
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
          // this.bootsForm.controls['plec'].setValue(gender[this.toEditBootsPack.boots.IdPlec - 1].Id);
        } else {
          this.bootsForm.controls['plec'].setValue(gender[0].Id);
        }
      }
    );
  }

  showDialogDelete(indexBoots) {
    this.displayDialogDelete = true;
    this.idBootsToDelete = indexBoots;
  }

  addBoots() {
    console.log(this.bootsForm.value);
    this.bootsService.addBoots(this.bootsForm.value, this.isEditMode).subscribe(
      data => {
        if (data['msg']) {
          this.messageService.add({severity: 'success', summary: 'Buty', detail: data['msg']});
          this.isEditMode = false;
          this.initBootsForm(true);
        } else  if (data['error'])  {
          this.messageService.add({severity: 'error', summary: 'Buty', detail: data['error']});
        }
      },
      error => {
        this.messageService.add({severity: 'error', summary: 'Buty', detail: error});
      }
    );
  }

  editBoots(index: number, amount: number) {
    this.bootsService.getOneBoots(index).subscribe(
      data => {
        // this.toEditSnowboard = data[0];
        this.bootsService.setEditBoots(data[0], amount);
        // console.log(this.toEditSnowboard);
        // console.log(this.toEditSnowboard.Model);
        this.router.navigateByUrl('/zarzadzanie/(adminContent:buty-formularz)');
        // console.log(this.toEditSnowboard);

      },
      error => this.messageService.add({severity: 'error', summary: 'Buty', detail: 'Nie udało się pobrać sprzętu'})
    );
  }

  deleteBoots(index: number) {
    this.displayDialogDelete = false;
    this.bootsService.deleteBoots(index).subscribe(
      data => {
        if (data['msg']) {
          this.messageService.add({severity: 'success', summary: 'Buty', detail: data['msg']});
          this.getAllBoots(false);
        } else  if (data['error'])  {
          this.messageService.add({severity: 'error', summary: 'Buty', detail: data['error']});
        }
      },
      error => console.log(error)
    );
  }

}
