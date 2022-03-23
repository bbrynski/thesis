import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';

const httpOptions = {
  headers: new HttpHeaders({ 'Content-Type': 'application/x-www-form-urlencoded' })
};

@Injectable({
  providedIn: 'root'
})
export class EquipmentService {



  private urlEquipment = 'http://localhost/repository/pracainz/PracaInzBackend/Sprzet';

  constructor(private http: HttpClient) { }

  getAllEquipmentToService(): Observable<any[]> {
    return this.http.get<any>(this.urlEquipment + '/serwis', httpOptions);
  }

  getStatistics(value: any): Observable<any[]> {
    return this.http.post<any[]>(this.urlEquipment + '/statystyki', value, httpOptions);
  }
}
