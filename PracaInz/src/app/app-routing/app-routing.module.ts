import { NgModule } from '@angular/core';
import { Routes, RouterModule} from '@angular/router';
import { SnowboardComponent } from '../snowboard/snowboard.component';
import {SkisComponent } from '../skis/skis.component';
import { AdminPanelComponent} from '../admin-panel/admin-panel.component';
import { SkiPolesComponent } from '../ski-poles/ski-poles.component';
import { BootsComponent } from '../boots/boots.component';
import { ProducerComponent } from '../producer/producer.component';
import { CustomerComponent } from '../customer/customer.component';
import { EmployeeComponent } from '../employee/employee.component';
import { AccessComponent } from '../access/access.component';
import { ReservationComponent } from '../reservation/reservation.component';
import { RentComponent } from '../rent/rent.component';
import { UserComponent } from '../user/user.component';
import { ServiceComponent } from '../service/service.component';
import { StatisticsComponent } from '../statistics/statistics.component';
import { ErrorsComponent } from '../errors/errors.component';
import { OfferComponent } from '../offer/offer.component';

const routes: Routes = [
  { path: '', component: OfferComponent },
  { path: 'snowboard', component: SnowboardComponent },
  { path: 'narty', component: SkisComponent },
  { path: 'kijki', component: SkiPolesComponent },
  { path: 'buty', component: BootsComponent },
  { path: 'producent', component: ProducerComponent },
  { path: 'logowanie', component: AccessComponent },
  { path: 'serwis', component: ServiceComponent },
  { path: 'statystyki', component: StatisticsComponent },
  { path: 'error', component: ErrorsComponent },
  { path: 'rezerwacja', component: ReservationComponent, children: [
    { path: 'lista', component: ReservationComponent},
  ] },
  { path: 'wypozyczenie', component: RentComponent },
  { path: 'zarzadzanie', component: AdminPanelComponent, children: [
    { path: 'snowboard-spis', component: SnowboardComponent, outlet: 'adminContent' },
    { path: 'snowboard-formularz', component: SnowboardComponent, outlet: 'adminContent' },
    { path: 'narty-spis', component: SkisComponent, outlet: 'adminContent' },
    { path: 'narty-formularz', component: SkisComponent, outlet: 'adminContent' },
    { path: 'kijki-spis', component: SkiPolesComponent, outlet: 'adminContent' },
    { path: 'kijki-formularz', component: SkiPolesComponent, outlet: 'adminContent' },
    { path: 'buty-spis', component: BootsComponent, outlet: 'adminContent' },
    { path: 'buty-formularz', component: BootsComponent, outlet: 'adminContent' },
    { path: 'producent-spis', component: ProducerComponent, outlet: 'adminContent' },
    { path: 'producent-formularz', component: ProducerComponent, outlet: 'adminContent' },
    { path: 'klient-spis', component: CustomerComponent, outlet: 'adminContent' },
    { path: 'klient-formularz', component: CustomerComponent, outlet: 'adminContent' },

    { path: 'pracownik-spis', component: EmployeeComponent, outlet: 'adminContent' },
    { path: 'pracownik-formularz', component: EmployeeComponent, outlet: 'adminContent' },
    { path: 'uzytkownik-spis', component: UserComponent, outlet: 'adminContent' },
    { path: 'uzytkownik-formularz', component: UserComponent, outlet: 'adminContent' },

    { path: 'rezerwacja-lista', component: ReservationComponent, outlet: 'adminContent' },
  ]},
];

@NgModule({
  imports: [ RouterModule.forRoot(routes) ],
  exports: [ RouterModule ]
})
export class AppRoutingModule { }
