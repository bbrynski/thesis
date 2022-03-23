import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';

const httpOptions = {
  headers: new HttpHeaders({ 'Content-Type': 'application/x-www-form-urlencoded' })
};

@Injectable({
  providedIn: 'root'
})
export class ServiceService {

  private serviceUrl = 'http://localhost/repository/pracainz/PracaInzBackend/Serwis';

  constructor(private http: HttpClient) { }

  getAllServiceActivity(): Observable<any[]> {
    return this.http.get<any[]>(this.serviceUrl + '/czynnosci');
  }

  addServiceActivity(serviceActivity: any): Observable<any[]> {
    return this.http.post<any>(this.serviceUrl + '/wstaw', serviceActivity, httpOptions);
  }

  getOneServiceEquipmentHistory(id: number): Observable<any[]> {
    return this.http.get<any[]>(this.serviceUrl + '/' + id);
  }
}
