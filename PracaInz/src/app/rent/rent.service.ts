import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';

const httpOptions = {
  headers: new HttpHeaders({ 'Content-Type': 'application/x-www-form-urlencoded' })
};

@Injectable({
  providedIn: 'root'
})
export class RentService {

  private rentUrl = 'http://localhost/repository/pracainz/PracaInzBackend/Wypozyczenia';
  idReservation: number[];
  idSnowboard: number;
  idSkis: number;
  idSkiPoles: number;
  idBoots: number;
  fromView = false;

  constructor(private http: HttpClient) { }

  addRental(equipment: any): Observable<any[]> {
    return this.http.post<any>(this.rentUrl + '/dodaj', equipment, httpOptions);
  }

  getAllRentals(option): Observable<any[]> {
    return this.http.post<any>(this.rentUrl, option, httpOptions);
  }

  setIdReservationArray(array: number[]) {
    this.idReservation = array;
  }
  getIdReservationArray() {
    return this.idReservation;
  }

  getFormView() {
    return this.fromView;
  }

  setFormView(value: boolean) {
    this.fromView = value;
  }

  setIdEquipentToRent(index: number, type: string) {
    if (type === 'snowboard') {
      this.idSnowboard = index;
    }
    if (type === 'skis') {
      this.idSkis = index;
    }
    if (type === 'skiPoles') {
      this.idSkiPoles = index;
    }
    if (type === 'boots') {
      this.idBoots = index;
    }
  }

  getIdEquipmentToRent(type: string) {
    if (type === 'snowboard') {
      return this.idSnowboard;
    }
    if (type === 'skis') {
      return this.idSkis;
    }
    if (type === 'skiPoles') {
      return this.idSkiPoles;
    }
    if (type === 'boots') {
      return this.idBoots;
    }
  }

  returnEquipment(ids) {
    return this.http.post<any>(this.rentUrl + '/zakoncz', ids, httpOptions);
  }
}
