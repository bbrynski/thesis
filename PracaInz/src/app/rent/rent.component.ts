import { Component, OnInit, Inject } from '@angular/core';

import { RentService } from './rent.service';
import { ReservationService } from '../reservation/reservation.service';
import { CustomerService } from '../customer/customer.service';
import { UserService } from '../user/user.service';
import { SnowboardService } from '../snowboard/snowboard.service';
import { SkisService } from '../skis/skis.service';
import { SkiPolesService } from '../ski-poles/ski-poles.service';
import { BootsService } from '../boots/boots.service';

import { Customer } from '../shared/customer';

import { FormControl, FormGroup, Validators } from '@angular/forms';
import { CustomValidators } from '../validators/customValidators';
import { FormCheckService } from '../validators/form-check.service';
import {Observable} from 'rxjs';
import {map, startWith} from 'rxjs/operators';
import { LOCAL_STORAGE, StorageService } from 'angular-webstorage-service';
import {MessageService} from 'primeng/api';
import { Router } from '@angular/router';


export interface State {
  flag: string;
  name: string;
  population: string;
}

@Component({
  selector: 'app-rent',
  templateUrl: './rent.component.html',
  styleUrls: ['./rent.component.css']
})
export class RentComponent implements OnInit {

  activeTab: number;
  reservation: any;

  rentForm: FormGroup;
  user: any;

  customers: Customer[];

  filteredCustomers: Observable<Customer[]>;

  reservedEquipment: number[];
  equipment: number[] = [];

  rentals: any[];

  snowboard: any;
  skis: any;
  skiPoles: any;
  boots: any;

  identyfikator: number;
  selectedStatus: string;
  statusList: any[];
  cols: any[];
  snowboardList: any[];
  skisList: any[];
  skiPolesList: any[];
  bootsList: any[];

  displayReturnDialog = false;
  idEquipmentCopy: number;
  idRentCopy: number;
  returnDate: string;
  formView: boolean;
  pl: any;

  formErrors = {
    cena: null,
    dataOd: null,
    dataDo: null,
    klient: null
  };

  constructor(private rentService: RentService, private reservationService: ReservationService,
    private customerService: CustomerService, private userService: UserService,
    @Inject(LOCAL_STORAGE) private localStorage: StorageService, private snowboardService: SnowboardService,
    private skisService: SkisService, private skiPolesService: SkiPolesService, private bootsService: BootsService,
    private messageService: MessageService, private formCheckService: FormCheckService, private router: Router) {

    }

  ngOnInit() {
    this.initLanguageCalendar();
    this.reservedEquipment = this.rentService.getIdReservationArray();
    this.formView = this.rentService.getFormView();
    this.rentService.setFormView(false);
    this.initStatusList();
    this.initRentForm();
    this.getAllRentals();
    this.getUser();
    this.getAllCustomers();
    this.getAllSnowboard(true);
    this.getAllSkis(true);
    this.getAllSkiPoles(true);
    this.getAllBoots(true);

    this.initHeaderTable();




   // console.log(this.rentService.getIdEquipmentToRent('snowboard'));
    if (this.reservedEquipment) {
      this.getReservedEquipment();

    } else if (this.rentService.getIdEquipmentToRent('snowboard')) {
      this.getSnowboardToRent(this.rentService.getIdEquipmentToRent('snowboard'));

    } else if (this.rentService.getIdEquipmentToRent('skis')) {
      this.getSkisToRent(this.rentService.getIdEquipmentToRent('skis'));

    } else if (this.rentService.getIdEquipmentToRent('skiPoles')) {
      this.getSkiPolesToRent(this.rentService.getIdEquipmentToRent('skiPoles'));

    } else if (this.rentService.getIdEquipmentToRent('boots')) {
      this.getBootsToRent(this.rentService.getIdEquipmentToRent('boots'));

    } else {
      this.activeTab = 0;
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

  initHeaderTable() {
    if (this.selectedStatus === 'active') {
      this.cols = [
        { field: 'DataOd', header: 'Data wypożyczenia' },
        { field: 'DataDo', header: 'Przywidywany zwrot' },
        { field: 'Nazwisko', header: 'Klient' },
        { field: 'Sprzet', header: 'Sprzęt' },
        { field: 'NazwaStatus', header: 'Stauts' }
    ];
    } else {
      this.cols = [
        { field: 'DataOd', header: 'Data wypożyczenia' },
        { field: 'DataZwrot', header: 'Data zwrotu'},
        { field: 'Nazwisko', header: 'Klient' },
        { field: 'Sprzet', header: 'Sprzęt' },
        { field: 'NazwaStatus', header: 'Stauts' }
    ];
    }
  }

  initStatusList() {
    this.statusList = [
      {label: 'Aktywne', value: 'active'},
      {label: 'Zakończone', value: 'ended'}
    ];
    this.selectedStatus = 'active';
  }

  getSnowboardToRent(index: number) {
    this.snowboardService.getOneSnowboard(index).subscribe(
      data => {
        this.snowboard = data[0];
        this.rentForm.controls['idSprzet'].setValue(data[0]['IdSnowboard']);
        this.rentForm.controls['snowboard'].setValue(true);
      }
    );

    this.activeTab = 1;
    this.rentService.setIdEquipentToRent(null, 'snowboard');
  }

  getSkisToRent(index: number) {
    this.skisService.getOneSkis(index).subscribe(
      data => {
        this.skis = data[0];
        this.rentForm.controls['idSprzet'].setValue(data[0]['IdNarty']);
        this.rentForm.controls['skis'].setValue(true);
      }
    );
    this.activeTab = 1;
    this.rentService.setIdEquipentToRent(null, 'skis');
  }

  getSkiPolesToRent(index: number) {
    this.skiPolesService.getOneSkiPoles(index).subscribe(
      data => {
        console.log('skis');
        console.log(data);

        this.skiPoles = data[0];
        this.rentForm.controls['idSprzet'].setValue(data[0]['IdKijki']);
        this.rentForm.controls['skiPoles'].setValue(true);
      }
    );
    this.activeTab = 1;
    this.rentService.setIdEquipentToRent(null, 'skiPoles');
  }

  getBootsToRent(index: number) {
    this.bootsService.getOneBoots(index).subscribe(
      data => {
        this.boots = data[0];
        this.rentForm.controls['idSprzet'].setValue(data[0]['IdButy']);
        this.rentForm.controls['boots'].setValue(true);
      }
    );
    this.activeTab = 1;
    this.rentService.setIdEquipentToRent(null, 'boots');
  }

  getReservedEquipment() {
    this.rentForm.controls['idRezerwacja'].setValue(true);
    this.reservedEquipment.forEach(element => {
      this.reservationService.getOneReservation(element).subscribe(
        data => {
          console.log(data);
          this.equipment.push(data['rezerwacja'][0]['IdSprzet']);
          this.rentForm.controls['idSprzet'].setValue(this.equipment);

          this.customers.forEach( customer => {
            if (customer['Id'] === data['rezerwacja'][0]['IdKlient']) {
              this.rentForm.controls['klient'].setValue(customer['Imie'] + ' ' + customer['Nazwisko']);
            }
          });

          this.rentForm.controls['idKlient'].setValue(data['rezerwacja'][0]['IdKlient']);
          this.rentForm.controls['klient'].disable();

          this.rentForm.controls['dataOd'].setValue(new Date(data['rezerwacja'][0]['DataOd']));
          this.rentForm.controls['DataOd'].setValue(new Date(data['rezerwacja'][0]['DataOd']));
          this.rentForm.controls['dataOd'].disable();

          this.rentForm.controls['dataDo'].setValue(new Date(data['rezerwacja'][0]['DataDo']));
          this.rentForm.controls['DataDo'].setValue(new Date(data['rezerwacja'][0]['DataDo']));
          this.rentForm.controls['dataDo'].disable();

          if (data['rezerwacja'][0]['IdSnowboard'] !== null) {
            this.snowboardService.getOneSnowboard(data['rezerwacja'][0]['IdSnowboard']).subscribe(
              dataSnowboard => {
                this.snowboard = dataSnowboard[0];
                this.rentForm.controls['cena'].setValue(dataSnowboard[0]['Cena']);
                console.log(this.snowboard);
              }
            );
          }

          if (data['rezerwacja'][0]['IdNarty'] !== null) {
            this.skisService.getOneSkis(data['rezerwacja'][0]['IdNarty']).subscribe(
              dataSkis => {
                this.skis = dataSkis[0];
                this.rentForm.controls['cena'].setValue(dataSkis[0]['Cena']);
                console.log(this.skis);
              }
            );
          }

          if (data['rezerwacja'][0]['IdKijki'] !== null) {
            this.skiPolesService.getOneSkiPoles(data['rezerwacja'][0]['IdKijki']).subscribe(
              dataSkiPoles => {
                this.skiPoles = dataSkiPoles[0];
                this.rentForm.controls['cena'].setValue(dataSkiPoles[0]['Cena']);
                console.log(this.skiPoles);
              }
            );
          }

          if (data['rezerwacja'][0]['IdButy'] !== null) {
            this.bootsService.getOneBoots(data['rezerwacja'][0]['IdButy']).subscribe(
              dataBoots => {
                this.boots = dataBoots[0];
                this.rentForm.controls['cena'].setValue(dataBoots[0]['Cena']);
                console.log(this.boots);
              }
            );
          }

        }
      );
    });
    this.rentService.setIdReservationArray(null);
    this.activeTab = 1;
  }



  initRentForm() {
    this.rentForm = new FormGroup({
      idRezerwacja: new FormControl(null),
      idSprzet: new FormControl(null),
      idKlient: new FormControl(null),
      klient: new FormControl(null, [Validators.required]),
      dataOd: new FormControl(null, [Validators.required]),
      dataDo: new FormControl(null, [Validators.required]),
      DataOd: new FormControl(null, [Validators.required]),
      DataDo: new FormControl(null, [Validators.required]),
      idPracownik: new FormControl(null),
      cena: new FormControl(null, [Validators.required, CustomValidators.validateNumeric]),
      pracownik: new FormControl({value: null, disabled: true}),
      snowboard: new FormControl(null),
      skis: new FormControl(null),
      skiPoles: new FormControl(null),
      boots: new FormControl(null),
      rezerwacja: new FormControl(this.reservedEquipment)
    });

    this.rentForm.valueChanges.subscribe((data) => {
      this.formErrors = this.formCheckService.validateForm(this.rentForm, this.formErrors, true);
    });
  }
  update(index: number) {
    console.log('Id: ' + index);
    this.rentForm.controls['idKlient'].setValue(index);
  }

  getAllRentals() {
    this.initHeaderTable();
    const tmp = {'status': this.selectedStatus};
    this.rentService.getAllRentals(tmp).subscribe(
      data => {
        this.rentals = data['rentals'];
        console.log(this.rentals);
      }
    );
  }

  getUser() {
    const user = this.localStorage.get('currentUser');
    if (user['user'] !== null) {
    this.userService.getUserByUsername(user['user']).subscribe(
      data => {
        console.log(data);
          this.user = data[0];
           this.rentForm.controls['idPracownik'].setValue(this.user['Id']);
           this.rentForm.controls['pracownik'].setValue(this.user['Imie'] + ' ' + this.user['Nazwisko']);
      }
    );
  } // wyloguj -> redirect na logowanie
}

  getAllCustomers() {
    this.customerService.getAllCustomer().subscribe(
      data => {
        if (!data['error']) {
          this.customers = data['customer'];
          this.filteredCustomers = this.rentForm.controls['klient'].valueChanges
          .pipe(
            startWith(''),
            map(customer => customer ? this._filterCustomers(customer) : this.customers.slice())
          );
        }
      }
    );
  }

  private _filterCustomers(value: string): Customer[] {
    const filterValue = value.toLowerCase();

    return this.customers.filter(customer => ((customer.Nazwisko + ' ' + customer.Imie).toLowerCase().indexOf(filterValue) === 0) ||
    ((customer.Imie + ' ' + customer.Nazwisko).toLowerCase().indexOf(filterValue) === 0));
  }

  addRent() {
    if (this.rentForm.controls['dataDo'].value < this.rentForm.controls['dataOd'].value) {
      this.messageService.add({severity: 'error', summary: 'Wypożyczenie', detail: 'Nieprawidłowa data końcowa'});
    } else {
      this.rentService.addRental(this.rentForm.value).subscribe(
        data => {
          console.log(data);
          if (data['error'] === null) {
            this.messageService.add({severity: 'success', summary: 'Wypożyczenie', detail: 'Wypożyczono sprzęt'});
            this.initRentForm();
          } else {
            this.messageService.add({severity: 'error', summary: 'Rezerwacja', detail: data['error']});
            this.getAllRentals();
          }
        }
      );
    }
  }

  returnEquipment(idEquipment: number, idRent: number, approval: boolean) {
    this.displayReturnDialog = false;
    const idToChange = { equipment: idEquipment, rent: idRent, flag: approval};
    this.idEquipmentCopy = idEquipment;
    this.idRentCopy = idRent;
    console.log(idToChange);
    this.rentService.returnEquipment(idToChange).subscribe(
      data => {
        console.log(data);
       if (data['msg'] !== null) {
        this.messageService.add({severity: 'success', summary: 'Wypożyczenie', detail: data['msg']});
        this.getAllRentals();
       } else if (data['error'] === 'Spoźniony zwrot') {
         this.displayReturnDialog = true;
         this.returnDate = data['planowanaData'];
       } else {
        this.messageService.add({severity: 'error', summary: 'Rezerwacja', detail: data['error']});
       }
      }
    );
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

}
