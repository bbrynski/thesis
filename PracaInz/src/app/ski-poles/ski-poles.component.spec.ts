import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { SkiPolesComponent } from './ski-poles.component';

describe('SkiPolesComponent', () => {
  let component: SkiPolesComponent;
  let fixture: ComponentFixture<SkiPolesComponent>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ SkiPolesComponent ]
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(SkiPolesComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
