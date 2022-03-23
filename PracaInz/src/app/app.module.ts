import { BrowserModule } from '@angular/platform-browser';
import { NgModule } from '@angular/core';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { AccordionModule } from 'primeng/accordion';
import { MenuModule, MenubarModule } from 'primeng/primeng';
import {PanelMenuModule} from 'primeng/panelmenu';
import {AppRoutingModule} from './app-routing/app-routing.module';
import { CommonModule } from '@angular/common';

import { AppComponent } from './app.component';
import { SnowboardComponent } from './snowboard/snowboard.component';
import { SnowboardService } from './snowboard/snowboard.service';
import { HttpClientModule } from '@angular/common/http';
import { AccessComponent } from './access/access.component';
import { AdminPanelComponent } from './admin-panel/admin-panel.component';

import { ReactiveFormsModule } from '@angular/forms';
import { DestinyComponent } from './destiny/destiny.component';
import { ProducerComponent } from './producer/producer.component';
import { GenderComponent } from './gender/gender.component';

import {ToastModule} from 'primeng/toast';
import {MessageService} from 'primeng/api';
import {DialogModule} from 'primeng/dialog';
import {ButtonModule} from 'primeng/button';
import { SkisComponent } from './skis/skis.component';
import { AccessService } from './access/access.service';
import { SkiPolesComponent } from './ski-poles/ski-poles.component';
import { BootsComponent } from './boots/boots.component';
import { CustomerComponent } from './customer/customer.component';
import { EmployeeComponent } from './employee/employee.component';
import { StorageServiceModule } from 'angular-webstorage-service';

import { HTTP_INTERCEPTORS } from '@angular/common/http';
import { JwtInterceptor } from './interceptors/jwt.interceptor';
import { ReservationComponent } from './reservation/reservation.component';

import {StepsModule} from 'primeng/steps';
import {MatButtonModule, MatCheckboxModule, MatNativeDateModule } from '@angular/material';
import {MatStepperModule} from '@angular/material/stepper';
import {MatTabsModule} from '@angular/material/tabs';
import {MatListModule} from '@angular/material/list';
import {MatExpansionModule} from '@angular/material/expansion';
import {MatDatepickerModule} from '@angular/material/datepicker';
import { FormsModule } from '@angular/forms';
import {MatFormFieldModule} from '@angular/material/form-field';
import {CalendarModule} from 'primeng/calendar';
import {MatCardModule} from '@angular/material/card';
import {AutoCompleteModule} from 'primeng/autocomplete';
import {MatAutocompleteModule} from '@angular/material/autocomplete';
import {MatInputModule} from '@angular/material';

import { RecaptchaModule } from 'ng-recaptcha';
import { RecaptchaFormsModule } from 'ng-recaptcha/forms';
import { RentComponent } from './rent/rent.component';
import { UserComponent } from './user/user.component';
import { LOCALE_ID } from '@angular/core';
import { registerLocaleData } from '@angular/common';
import localepl from '@angular/common/locales/pl';
import { ServiceComponent } from './service/service.component';

import {ToggleButtonModule} from 'primeng/togglebutton';
import { StatisticsComponent } from './statistics/statistics.component';
import {SelectButtonModule} from 'primeng/selectbutton';
import {ChartModule} from 'primeng/chart';
import {RadioButtonModule} from 'primeng/radiobutton';
import {TableModule} from 'primeng/table';
import {DropdownModule} from 'primeng/dropdown';
import { ErrorsComponent } from './errors/errors.component';
import { OfferComponent } from './offer/offer.component';


registerLocaleData(localepl);


@NgModule({
  declarations: [
    AppComponent,
    SnowboardComponent,
    AccessComponent,
    AdminPanelComponent,
    DestinyComponent,
    ProducerComponent,
    GenderComponent,
    SkisComponent,
    SkiPolesComponent,
    BootsComponent,
    CustomerComponent,
    EmployeeComponent,
    ReservationComponent,
    RentComponent,
    UserComponent,
    ServiceComponent,
    StatisticsComponent,
    ErrorsComponent,
    OfferComponent
    ],
  imports: [
    BrowserModule,
    BrowserAnimationsModule,
    MenuModule,
    AccordionModule,
    MenubarModule,
    PanelMenuModule,
    HttpClientModule,
    AppRoutingModule,
    ReactiveFormsModule,
    CommonModule,
    ToastModule,
    DialogModule,
    ButtonModule,
    StorageServiceModule,
    StepsModule,
    MatButtonModule,
    MatCheckboxModule,
    MatStepperModule,
    MatTabsModule,
    MatListModule,
    MatExpansionModule,
    MatDatepickerModule,
    FormsModule,
    MatNativeDateModule,
    MatFormFieldModule,
    CalendarModule,
    MatCardModule,
    RecaptchaModule,
    RecaptchaFormsModule,
    AutoCompleteModule,
    MatAutocompleteModule,
    MatInputModule,
    ToggleButtonModule,
    SelectButtonModule,
    ChartModule,
    RadioButtonModule,
    TableModule,
    DropdownModule
  ],
  providers: [SnowboardService, MessageService, AccessService, {
    provide: HTTP_INTERCEPTORS,
    useClass: JwtInterceptor,
    multi: true
  }, MatDatepickerModule,
  { provide: LOCALE_ID, useValue: 'pl' }],
  bootstrap: [AppComponent]
})
export class AppModule { }
