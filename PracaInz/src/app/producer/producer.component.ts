import { Component, OnInit } from '@angular/core';
import { ProducerService } from '../producer/producer.service';
import { FormCheckService } from '../validators/form-check.service';
import {MessageService} from 'primeng/api';
import {AppRoutingService} from '../app-routing/app-routing.service';
import { Producer } from '../shared/producer';
import { Router } from '@angular/router';
import { FormControl, FormGroup, Validators } from '@angular/forms';
import { CustomValidators } from '../validators/customValidators';

@Component({
  selector: 'app-producer',
  templateUrl: './producer.component.html',
  styleUrls: ['./producer.component.css']
})
export class ProducerComponent implements OnInit {

  producer: Producer[];
  adminPanelView: any;
  producerForm: FormGroup;
  displayDialogDelete = false;
  idProducerToDelete: number;
  isEditMode: boolean;
  toEditProducer: any;
  formErrors = {
    nazwa: null,
  };
  cols: any[];

  constructor(private producerService: ProducerService, private appRoutingService: AppRoutingService,
    private formCheckService: FormCheckService, private messageService: MessageService,
    private router: Router) { }

  ngOnInit() {
    this.adminPanelView = this.appRoutingService.checkAdminPanelUrl(this.router.url);
    this.isEditMode = this.producerService.getIsEditMode();
    if (this.isEditMode) {
      this.producerService.changeEditMode();
      this.toEditProducer = this.producerService.getEditProducer();
    }
    this.cols = [
      { field: 'NazwaProducent', header: 'Producent' }
  ];
    this.getAllProducer();
    this.initProducerForm();
  }

  initProducerForm() {
    this.producerForm = new FormGroup({
      id: new FormControl(null),
      nazwa: new FormControl(null, [Validators.required, CustomValidators.validateAlphanumeric, CustomValidators.validateLength]),
    });

    if (this.isEditMode) {
      this.producerForm.controls['id'].setValue(this.toEditProducer.Id);
      this.producerForm.controls['nazwa'].setValue(this.toEditProducer.NazwaProducent);
    }

    this.producerForm.valueChanges.subscribe((data) => {
      this.formErrors = this.formCheckService.validateForm(this.producerForm, this.formErrors, true);
    });
  }

  getAllProducer(): void {
    this.producerService.getAllProducer().subscribe(
      producer => {
        this.producer = producer;
      }
    );
  }

  addProducer() {
    this.producerService.addProducer(this.producerForm.value, this.isEditMode).subscribe(
      data => {
        console.log(data);
        if (data['msg']) {
          this.messageService.add({severity: 'success', summary: 'Producent', detail: data['msg']});
          this.isEditMode = false;
          this.initProducerForm();
        } else  if (data['error'])  {
          this.messageService.add({severity: 'error', summary: 'Producent', detail: data['error']});
        }
      },
      error => {
        this.messageService.add({severity: 'error', summary: 'Producent', detail: error});
      }
    );
  }

  editProducer(index: number) {
    this.producerService.getOneProducer(index).subscribe(
      data => {
        // this.toEditSnowboard = data[0];
        this.producerService.setEditProducer(data[0]);
        // console.log(this.toEditSnowboard);
        // console.log(this.toEditSnowboard.Model);
        this.router.navigateByUrl('/zarzadzanie/(adminContent:producent-formularz)');
        // console.log(this.toEditSnowboard);

      },
      error => {
        console.log(error);
        this.messageService.add({severity: 'error', summary: 'Producent', detail: 'Nie udało się pobrać sprzętu'});
      }
    );
  }

  deleteProducer(index: number) {
    this.displayDialogDelete = false;
    this.producerService.deleteProducer(index).subscribe(
      data => {
        if (data['msg']) {
          this.messageService.add({severity: 'success', summary: 'Producent', detail: data['msg']});
          this.getAllProducer();
        } else  if (data['error'])  {
          this.messageService.add({severity: 'error', summary: 'Producent', detail: data['error']});
        }
      },
      error => console.log(error)
    );
  }

  showDialogDelete(indexProducer) {
    this.displayDialogDelete = true;
    this.idProducerToDelete = indexProducer;
  }

}
