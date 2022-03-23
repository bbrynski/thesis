import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';

const httpOptions = {
  headers: new HttpHeaders({ 'Content-Type': 'application/x-www-form-urlencoded' })
};

@Injectable({
  providedIn: 'root'
})
export class ReservationService {
  private reservationUrl = 'http://localhost/repository/pracainz/PracaInzBackend/Rezerwacja';
  constructor(private http: HttpClient) { }

addReservation(reservation: any): Observable<any[]> {
    return this.http.post<any>(this.reservationUrl + '/dodaj', reservation, httpOptions);
  }

getAllReservation(period: any): Observable<any[]> {
    return this.http.post<any>(this.reservationUrl, period, httpOptions);
  }

  changeStatusReservation(reservation: any) {
    return this.http.post<any>(this.reservationUrl + '/zmien', reservation, httpOptions);
  }

  deleteReservation(index: number): Observable<any[]> {
    return this.http.delete<any[]>(this.reservationUrl + '/usun/' + index);
  }

  getOneReservation(index: number): Observable<any> {
    return this.http.get<any[]>(this.reservationUrl + '/' + index);
  }

  getSkisOptions(index: number): Observable<any> {
    return this.http.get<any[]>(this.reservationUrl + '/nartyopcje/' + index);
  }

  getSnowboardOptions(index: number): Observable<any> {
    return this.http.get<any[]>(this.reservationUrl + '/deskaopcje/' + index);
  }



}
