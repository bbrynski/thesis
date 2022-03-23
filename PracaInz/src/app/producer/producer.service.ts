import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders } from '@angular/common/http';
import { Observable } from 'rxjs';
import { Producer } from '../shared/producer';

const httpOptions = {
  headers: new HttpHeaders({ 'Content-Type': 'application/x-www-form-urlencoded' })
};

@Injectable({
  providedIn: 'root'
})
export class ProducerService {
  private urlProducer = 'http://localhost/repository/pracainz/PracaInzBackend/Producent';
  private isEditMode = false;
  private toEditProducer: Producer;
  constructor(private http: HttpClient) { }

  getAllProducer(): Observable<Producer[]> {
    return this.http.get<Producer[]>(this.urlProducer);
  }

  getOneProducer(index: number): Observable<any> {
    return this.http.get<any[]>(this.urlProducer + '/' + index);
  }

  addProducer(producer: any, isEditMode: boolean): Observable<any> {
    if (isEditMode) {
      return this.http.post<any>(this.urlProducer + '/edytuj', producer, httpOptions);
    } else {
      return this.http.post<any>(this.urlProducer + '/wstaw', producer, httpOptions);
    }
  }

  deleteProducer(index: number): Observable<any[]> {
    return this.http.delete<any[]>(this.urlProducer + '/usun/' + index);
  }

  changeEditMode() {
    this.isEditMode = !this.isEditMode;
  }
  getIsEditMode() {
    return this.isEditMode;
  }
  setEditProducer(producer: Producer) {
    this.toEditProducer = producer;
    this.changeEditMode();
  }
  getEditProducer() {
    return this.toEditProducer;
  }

}
