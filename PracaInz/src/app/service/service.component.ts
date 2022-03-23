import { Component, OnInit, Inject } from '@angular/core';
import { EquipmentService } from '../equipment/equipment.service';
import { FormControl, FormGroup, Validators } from '@angular/forms';

import { SnowboardService } from '../snowboard/snowboard.service';
import { SkisService } from '../skis/skis.service';
import { SkiPolesService } from '../ski-poles/ski-poles.service';
import { BootsService } from '../boots/boots.service';
import { UserService } from '../user/user.service';
import { ServiceService } from './service.service';

import { LOCAL_STORAGE, StorageService } from 'angular-webstorage-service';
import {MessageService, SelectItem} from 'primeng/api';

import { formatDate } from '@angular/common';

@Component({
  selector: 'app-service',
  templateUrl: './service.component.html',
  styleUrls: ['./service.component.css']
})
export class ServiceComponent implements OnInit {

  equipment: any[];
  serviceActivity: any[];
  equipmentHistory: any[];
  displayForm: boolean;
  displayHistory: boolean;
  serviceForm: FormGroup;

  snowboardList: any[];
  skisList: any[];
  skiPolesList: any[];
  bootsList: any[];
  cols: any[];
  colsHistory: any[];
  selectType: SelectItem[];

  constructor(private equipmentService: EquipmentService, private snowboardService: SnowboardService,
    @Inject(LOCAL_STORAGE) private localStorage: StorageService, private userService: UserService,
    private serviceService: ServiceService, private messageService: MessageService,
    private skisService: SkisService, private skiPolesService: SkiPolesService, private bootsService: BootsService) { }

  ngOnInit() {
    this.initServiceForm();
    this.displayForm = false;
    this.displayHistory = false;
    this.getAllEquipmentToService();
    this.getAllServiceActivity();
    this.getAllSnowboard(true);
    this.getAllSkis(true);
    this.getAllSkiPoles(true);
    this.getAllBoots(true);

    this.cols = [
      { field: 'Sprzet', header: 'Sprzęt' }
  ];
    this.colsHistory = [
      { field: 'Data', header: 'Data' },
      { field: 'NazwaCzynnosc', header: 'Wykonana czynność' },
      { field: 'Opis', header: 'Opis' },
      { field: 'Nazwisko', header: 'Pracownik' }
    ];

    this.selectType = [
      { label: 'brak filtru', value: null },
      { label: 'Smarowanie', value: 'Smarowanie' },
      { label: 'Ostrzenie', value: 'Ostrzenie' },
      { label: 'Ostrzenie + smarowanie', value: 'Ostrzenie + smarowanie' },
      { label: 'Regeneracja', value: 'Regeneracja' },
      { label: 'Inne', value: 'Inne' }
  ];
  }

  initServiceForm() {
    this.serviceForm = new FormGroup({
      idSprzet: new FormControl(null),
      idPracownik: new FormControl(null),
      pracownik: new FormControl({value: null, disabled: true}),
      idCzynnosc: new FormControl(null),
      data: new FormControl(formatDate(new Date(), 'shortDate', 'pl')),
      opis: new FormControl(null)
    });
  }

  // formatDate(this.searchByDateForm.controls['dataOd'].value, 'shortDate', 'pl')

  getAllEquipmentToService() {
    this.equipmentService.getAllEquipmentToService().subscribe(
      data => {
        console.log(data);
        this.equipment = data['equipment'];
        console.log(this.equipment);
      }
    );
  }

  getAllServiceActivity() {
    this.serviceService.getAllServiceActivity().subscribe(
      data => {
        this.serviceActivity = data;
        this.serviceForm.controls['idCzynnosc'].setValue(this.serviceActivity[0].Id);
      }
    );
  }

  changeDisplayForm(idEquipment: number, id: number, type: string) {
    this.displayForm = !this.displayForm;
    this.getUser();
    this.serviceForm.controls['idSprzet'].setValue(idEquipment);
  }

  getUser() {
    const user = this.localStorage.get('currentUser');
    if (user['user'] !== null) {
    this.userService.getUserByUsername(user['user']).subscribe(
      data => {
           this.serviceForm.controls['idPracownik'].setValue(data[0]['Id']);
           this.serviceForm.controls['pracownik'].setValue(data[0]['Imie'] + ' ' + data[0]['Nazwisko']);
      });
    }
  }

  addServisActivity() {
    this.serviceService.addServiceActivity(this.serviceForm.value).subscribe(
      data => {

        if (data['error'] === null) {
          this.messageService.add({severity: 'success', summary: 'Serwis', detail: data['msg']});
          this.getAllEquipmentToService();
        } else {
          this.messageService.add({severity: 'error', summary: 'Serwis', detail: data['error']});
        }
      }
    );
  }

  getEquipmentHistory(id: number) {
    this.serviceService.getOneServiceEquipmentHistory(id).subscribe(
      data => {
        console.log(data);
        if (data['error'] === null) {
          this.equipmentHistory = data['history'];
          this.displayHistory = !this.displayHistory;
        } else {
          this.messageService.add({severity: 'info', summary: 'Historia serwisowa', detail: data['error']});
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
