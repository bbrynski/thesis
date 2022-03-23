import { Injectable } from '@angular/core';
import { Observable } from 'rxjs';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { SkiPoles } from '../shared/ski-poles';

const httpOptions = {
  headers: new HttpHeaders({ 'Content-Type': 'application/x-www-form-urlencoded' })
};

@Injectable({
  providedIn: 'root'
})
export class SkiPolesService {

  private skiPolesUrl = 'http://localhost/repository/pracainz/PracaInzBackend/Kijki';
  private isEditMode = false;
  private toEditSkiPolesPack = {
    skiPoles: null,
    amount: null
  };

  constructor(private http: HttpClient) { }

  getByDate(value: any): Observable<any[]> {
    return this.http.post<any[]>(this.skiPolesUrl + '/data', value, httpOptions);
  }

  getAllSkiPoles(single: boolean): Observable<any[]> {
    if (single) {
      return this.http.get<any[]>(this.skiPolesUrl + '/pojedyncze');
    } else {
      return this.http.get<any[]>(this.skiPolesUrl);
    }
  }

  getOneSkiPoles(index: number): Observable<any> {
    return this.http.get<any[]>(this.skiPolesUrl + '/' + index);
  }

  addSkiPoles(skisPoles: any, isEditMode: boolean): Observable<any> {
    if (isEditMode) {
      return this.http.post<any>(this.skiPolesUrl + '/edytuj', skisPoles, httpOptions);
    } else {
      return this.http.post<any>(this.skiPolesUrl + '/wstaw', skisPoles, httpOptions);
    }
  }

  setEditSkiPoles(skiPoles: SkiPoles, amount: number) {
    this.toEditSkiPolesPack.skiPoles = skiPoles;
    this.toEditSkiPolesPack.amount = amount;
    this.changeEditMode();
  }

  changeEditMode() {
    this.isEditMode = !this.isEditMode;
  }

  getIsEditMode() {
    return this.isEditMode;
  }

  getEditSkiPoles() {
    return this.toEditSkiPolesPack;
  }

  deleteSkiPoles(index: number): Observable<any[]> {
    return this.http.delete<any[]>(this.skiPolesUrl + '/usun/' + index);
  }

}
