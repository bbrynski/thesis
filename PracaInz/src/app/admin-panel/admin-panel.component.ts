import { Component, OnInit } from '@angular/core';
import { FormControl, FormGroup } from '@angular/forms';
import { Type } from '../shared/destiny';
import { Producer } from '../shared/producer';
import { Snowboard } from '../shared/snowboard';
import { Gender } from '../shared/gender';
import { DestinyService } from '../destiny/destiny.service';
import { ProducerService } from '../producer/producer.service';
import { SnowboardService } from '../snowboard/snowboard.service';
import { GenderService } from '../gender/gender.service';
import { AccessService } from '../access/access.service';

import {MenuItem} from 'primeng/api';


@Component({
  selector: 'app-admin-panel',
  templateUrl: './admin-panel.component.html',
  styleUrls: ['./admin-panel.component.css']
})
export class AdminPanelComponent implements OnInit {
  destiny: Type[];
  producer: Producer[];
  gender: Gender[];
  snowboardForm: FormGroup;

  items: MenuItem[];

  isAdmin = false;
  isEmployee = false;
  isOwner = false;


  constructor(private destinyService: DestinyService, private producerService: ProducerService,
    private snowboardService: SnowboardService, private genderService: GenderService, private accessService: AccessService) { }

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
    this.initMenu();
    this.getAllDestiny();
    this.getAllProducer();
    this.getAllGender();
    this.initSnowboardForm();
  }

  initMenu() {
    if (this.isEmployee) {
      this.items = [
        {
          label: 'Snowboard',
          items: [{
            label: 'Lista modeli',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['snowboard-spis']}}],
            }]
        },
        {
          label: 'Narty',
          items: [{
            label: 'Lista modeli',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['narty-spis']}}],
            }]
        },
        {
          label: 'Kijki',
          items: [{
            label: 'Lista modeli',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['kijki-spis']}}],
            }]
        },
        {
          label: 'Buty',
          items: [{
            label: 'Lista modeli',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['buty-spis']}}],
            }]
        },
        {
          label: 'Klient',
          items: [{
            label: 'Lista klientów',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['klient-spis']}}],
            },
            {
            label: 'Formularz dodawania',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['klient-formularz']}}],
            }
          ]
        },
        {
          label: 'Rezerwacje',
          routerLink: ['/zarzadzanie', {outlets: {adminContent: ['rezerwacja-lista']}}]
        }
      ];
    } else if (this.isOwner) {
      this.items = [
        {
          label: 'Snowboard',
          items: [{
            label: 'Lista modeli',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['snowboard-spis']}}],
            },
            {
            label: 'Formularz dodawania',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['snowboard-formularz']}}],
            }
          ]
        },
        {
          label: 'Narty',
          items: [{
            label: 'Lista modeli',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['narty-spis']}}],
            },
            {
            label: 'Formularz dodawania',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['narty-formularz']}}],
            }
          ]
        },
        {
          label: 'Kijki',
          items: [{
            label: 'Lista modeli',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['kijki-spis']}}],
            },
            {
            label: 'Formularz dodawania',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['kijki-formularz']}}],
            }
          ]
        },
        {
          label: 'Buty',
          items: [{
            label: 'Lista modeli',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['buty-spis']}}],
            },
            {
            label: 'Formularz dodawania',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['buty-formularz']}}],
            }
          ]
        },
        {
          label: 'Producent',
          items: [{
            label: 'Lista producentów',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['producent-spis']}}],
            },
            {
            label: 'Formularz dodawania',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['producent-formularz']}}],
            }
          ]
        },
        {
          label: 'Klient',
          items: [{
            label: 'Lista klientów',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['klient-spis']}}],
            },
            {
            label: 'Formularz dodawania',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['klient-formularz']}}],
            }
          ]
        },
        {
          label: 'Uzytkownik',
          items: [{
            label: 'Lista użytkowników',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['uzytkownik-spis']}}],
            },
            {
            label: 'Formularz dodawania',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['uzytkownik-formularz']}}],
            }
          ]
        },
        {
          label: 'Rezerwacje',
          routerLink: ['/zarzadzanie', {outlets: {adminContent: ['rezerwacja-lista']}}]
        }
      ];
    } else if (this.isAdmin) {
      this.items = [
        {
          label: 'Snowboard',
          items: [{
            label: 'Lista modeli',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['snowboard-spis']}}],
            },
            {
            label: 'Formularz dodawania',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['snowboard-formularz']}}],
            }
          ]
        },
        {
          label: 'Narty',
          items: [{
            label: 'Lista modeli',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['narty-spis']}}],
            },
            {
            label: 'Formularz dodawania',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['narty-formularz']}}],
            }
          ]
        },
        {
          label: 'Kijki',
          items: [{
            label: 'Lista modeli',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['kijki-spis']}}],
            },
            {
            label: 'Formularz dodawania',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['kijki-formularz']}}],
            }
          ]
        },
        {
          label: 'Buty',
          items: [{
            label: 'Lista modeli',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['buty-spis']}}],
            },
            {
            label: 'Formularz dodawania',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['buty-formularz']}}],
            }
          ]
        },
        {
          label: 'Producent',
          items: [{
            label: 'Lista producentów',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['producent-spis']}}],
            },
            {
            label: 'Formularz dodawania',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['producent-formularz']}}],
            }
          ]
        },
        {
          label: 'Klient',
          items: [{
            label: 'Lista klientów',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['klient-spis']}}],
            }
          ]
        },
        {
          label: 'Uzytkownik',
          items: [{
            label: 'Lista użytkowników',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['uzytkownik-spis']}}],
            },
            {
            label: 'Formularz dodawania',
            routerLink: ['/zarzadzanie', {outlets: {adminContent: ['uzytkownik-formularz']}}],
            }
          ]
        }
      ];
    }
  }


  initSnowboardForm() {
    this.snowboardForm = new FormGroup({
      model: new FormControl(null),
      przeznaczenie: new FormControl(null),
      dlugosc: new FormControl(null),
      producent: new FormControl(null),
      cena: new FormControl(null),
      plec: new FormControl(null),
      ilosc: new FormControl(null)
    });
  }

  getAllDestiny(): void {
    this.destinyService.getAllDestiny().subscribe(destiny => this.destiny = destiny);
  }

  getAllProducer(): void {
    this.producerService.getAllProducer().subscribe(producer => this.producer = producer);
  }

  getAllGender(): void {
    this.genderService.getAllGender().subscribe(gender => this.gender = gender);
  }

  addSnowboard() {
    console.log(this.snowboardForm.value);
    this.snowboardService.addSnowboard(this.snowboardForm.value, false).subscribe(
      data => console.log(data),
    error => console.log(error)
    );
  }

}
