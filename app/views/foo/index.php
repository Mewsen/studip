<form action="#" class="default">
    <fieldset>
        <legend>date</legend>

        <label>
            Date
            <input type="date" name="date"
                   value="<?= htmlReady(Request::get('date')) ?>">
        </label>
    </fieldset>
    <fieldset>
        <legend>Datetime local</legend>

        <label>
            Date
            <input type="datetime-local" name="datetime-local"
                   value="<?= htmlReady(Request::get('datetime-local')) ?>">
        </label>
    </fieldset>
    <fieldset>
        <legend>Time</legend>

        <label>
            Time
            <input type="time" name="time"
                   value="<?= htmlReady(Request::get('time')) ?>">
        </label>

        <label class="col-3">
            A < B
            <input type="time" name="time-a" id="time-a"
                   value="<?= htmlReady(Request::get('time-a')) ?>"
                   data-time-picker='{"<":"#time-b"}'>
        </label>

        <label class="col-3">
            B > A
            <input type="time" name="time-b" id="time-b"
                   value="<?= htmlReady(Request::get('time-b')) ?>"
                   data-time-picker='{">":"#time-a"}'>
        </label>

    </fieldset>

    <footer>
        <button type="submit">submit</button>
    </footer>
</form>
