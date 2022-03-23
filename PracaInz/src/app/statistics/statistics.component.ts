import { Component, OnInit } from '@angular/core';

import { EquipmentService } from '../equipment/equipment.service';
import { MessageService } from 'primeng/api';
@Component({
  selector: 'app-statistics',
  templateUrl: './statistics.component.html',
  styleUrls: ['./statistics.component.css']
})
export class StatisticsComponent implements OnInit {

  selectedEquipment: string;
  equipment: any[];


  selectedOption: string;
  selectedGender: string;
  options: any[] = [];
  gender: any[] = [];

  dataOd: Date;
  dataDo: Date;

  data: any;

  valuePack = {
    dateFrom: null,
    dateTo: null,
    equipmentType: null,
    options: null,
    gender: null
  };

  model: any[] = [];
  dlugosc: any[] = [];
  rozmiar: any[] = [];
  producent: any[] = [];

  isModel: boolean;
  isDlugosc: boolean;
  isRozmiar: boolean;
  isData: boolean;
  isProducent: boolean;
  isPlec: boolean;
  pl: any;

  constructor(private equipmentService: EquipmentService, private messageService: MessageService) {}

  ngOnInit() {
    this.initEquipmentList();
    this.initLanguageCalendar();
    this.isModel = false;
    this.isDlugosc = false;
    this.isRozmiar = false;
    this.isData = false;
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

  initEquipmentList() {
    this.equipment = [
      {label: 'Wszystkie', value: 'all'},
      {label: 'Snowboard', value: 'snowboard'},
      {label: 'Narty', value: 'skis'},
      {label: 'Kijki', value: 'skiPoles'},
      {label: 'Buty', value: 'boots'}
    ];
  }

  initDataChart(dataServer) {
    if (dataServer['equipmentType'] === 'all') {
      if (dataServer['snowboard'] === 0 && dataServer['skis'] === 0 && dataServer['skiPoles'] === 0 && dataServer['boots'] === 0) {
        this.emptyResults();
      } else {
      this.isModel = false;
      this.isDlugosc = false;
      this.isRozmiar = false;
      this.isData = true;
      this.isProducent = false;
      this.data = {
        labels: ['Snowboard', 'Narty', 'Kijki', 'Buty'],
        datasets: [
          {
            data: [dataServer['snowboard'], dataServer['skis'], dataServer['skiPoles'], dataServer['boots']],
            backgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#94d189'
            ],
            hoverBackgroundColor: [
                '#FF6384',
                '#36A2EB',
                '#FFCE56',
                '#94d189'
            ]
        }
        ]
    };
    }
    } else if (dataServer['options'] === 'model') {
      if (!dataServer['model']) {
        this.emptyResults();
      } else {
        if (dataServer['gender'] !== 'all') {
          this.isPlec = true;
        } else {
          this.isPlec = false;
        }
        this.isModel = true;
        this.isDlugosc = false;
        this.isRozmiar = false;
        this.isData = false;
        this.isProducent = false;
        this.model = dataServer['model'];
      }
    } else if (dataServer['options'] === 'size') {
      if (!dataServer['rozmiar']) {
        this.emptyResults();
      } else {
        if (dataServer['gender'] !== 'all') {
          this.isPlec = true;
        } else {
          this.isPlec = false;
        }
        this.isModel = false;
        this.isDlugosc = false;
        this.isRozmiar = true;
        this.isData = false;
        this.isProducent = false;
        this.rozmiar = dataServer['rozmiar'];

      }
    } else if (dataServer['options'] === 'length')  {
      if (!dataServer['dlugosc']) {
        this.emptyResults();
      } else {
        if (dataServer['gender'] !== 'all') {
          this.isPlec = true;
        } else {
          this.isPlec = false;
        }
        this.dlugosc = dataServer['dlugosc'];
        this.isModel = false;
        this.isDlugosc = true;
        this.isRozmiar = false;
        this.isData = false;
        this.isProducent = false;
      }
    } else if (dataServer['options'] === 'gender')  {
      if (!dataServer['damska'] && !dataServer['meska']) {
        this.emptyResults();
      } else {
        this.isModel = false;
        this.isDlugosc = false;
        this.isRozmiar = false;
        this.isData = true;
        this.isProducent = false;
          this.data = {
            labels: ['Damska', 'Męska'],
            datasets: [
              {
                data: [dataServer['damska'], dataServer['meska']],
                backgroundColor: [
                    '#FF6384',
                    '#36A2EB'
                ],
                hoverBackgroundColor: [
                    '#FF6384',
                    '#36A2EB'
                ]
            }
            ]
        };
      }
    } else if (dataServer['options'] === 'type')  {
      if (!dataServer['all-mountain'] && !dataServer['all-round'] && !dataServer['freeride'] && !dataServer['freestyle']) {
        this.emptyResults();
      } else {
        this.isModel = false;
        this.isDlugosc = false;
        this.isRozmiar = false;
        this.isData = true;
        this.isProducent = false;
          this.data = {
            labels: ['All-mountain', 'All-round', 'Freeride', 'Freestyle'],
            datasets: [
              {
                data: [dataServer['all-mountain'], dataServer['all-round'], dataServer['freeride'], dataServer['freestyle']],
                backgroundColor: [
                  '#FF6384',
                  '#36A2EB',
                  '#FFCE56',
                  '#94d189'
                ],
                hoverBackgroundColor: [
                  '#FF6384',
                  '#36A2EB',
                  '#FFCE56',
                  '#94d189'
                ]
            }
            ]
          };
        }
    } else if (dataServer['equipmentType'] === 'producer') {
      if (!dataServer['producent']) {
        this.emptyResults();
      } else {
      this.isModel = false;
      this.isDlugosc = false;
      this.isRozmiar = false;
      this.isData = false;
      this.isProducent = true;
      this.producent = dataServer['producent'];
      }
    }
  }

  emptyResults() {
    this.isModel = false;
    this.isDlugosc = false;
    this.isRozmiar = false;
    this.isData = false;
    this.isProducent = false;
    this.isPlec = false;
    this.messageService.add({severity: 'info', summary: 'Statystyki', detail: 'brak wyników'});
  }

  generateChart() {
    let error = false;
    if (!this.dataOd || !this.dataDo || this.dataDo < this.dataOd) {
      error = true;
      this.messageService.add({severity: 'error', summary: 'Statystyki', detail: 'Nieprawidłowe daty'});
    }
    if (!this.selectedEquipment || (this.selectedEquipment !== 'all' && this.selectedEquipment !== 'producer' && !this.selectedOption)) {
      error = true;
      this.messageService.add({severity: 'error', summary: 'Statystyki', detail: 'Nie wybrano opcji generowania statystyk'});

    }

    if (!error) {
    this.valuePack.dateFrom = this.dataOd;
    this.valuePack.dateTo = this.dataDo;
    this.valuePack.equipmentType = this.selectedEquipment;
    this.valuePack.options = this.selectedOption;
    this.valuePack.gender = this.selectedGender;


    this.equipmentService.getStatistics(this.valuePack).subscribe(
      data => {
        console.log(data);
        this.initDataChart(data);
      }
    );
    }
  }

  updateOptions() {
    if (this.selectedEquipment === 'all') {
      this.options = [];
      this.gender = [];
      this.selectedOption = null;
      this.selectedGender = null;
    } else if (this.selectedEquipment === 'snowboard' || this.selectedEquipment === 'skis') {
      this.gender = [];
      this.options = [
        {label: 'Model', value: 'model'},
        {label: 'Płeć użytkownika', value: 'gender'},
        {label: 'Przeznaczenie', value: 'type'},
        {label: 'Długość', value: 'length'},
      ];
    } else if (this.selectedEquipment === 'skiPoles') {
      this.gender = [];
      this.options = [
        {label: 'Model', value: 'model'},
        {label: 'Płeć użytkownika', value: 'gender'},
        {label: 'Długość sprzętu', value: 'length'},
      ];
    } else if (this.selectedEquipment === 'boots') {
      this.gender = [];
      this.options = [
        {label: 'Model', value: 'model'},
        {label: 'Płeć użytkownika', value: 'gender'},
        {label: 'Rozmiar', value: 'size'},
      ];
    }

    if (this.selectedOption === 'model' || this.selectedOption === 'type'  || this.selectedOption === 'length'
    || this.selectedOption === 'size' ) {
      this.gender = [
        {label: 'Wszystkie', value: 'all'},
        {label: 'Damska', value: 'woman'},
        {label: 'Męska', value: 'man'},
      ];
    } else {
      this.gender = [];
    }
  }


}
